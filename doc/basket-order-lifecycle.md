# Basket and order lifecycle flow

This document traces the lifecycle of a basket and its conversion into an order in the legacy storefront architecture.

The main idea is:

- a user has a basket chain managed by `Shopping_Basket_Chain`
- each basket is a `Shopping_Basket` instance with content, state, version, and history
- the basket is persisted through `Data_Basket`
- when the customer confirms order creation, the basket is finalized and becomes an `Order`
- the order logic is then handled through `Order`, `Data_Order`, and related helpers

---

## 1. Entry: basket chain bootstrapping

The basket system is created during global bootstrap in [init.php](../init.php):

```php
$session_object = array(
    'P' => 'Person',
    'Info' => 'Info',
    'Shopping_Basket_Chain' => 'Shopping_Basket_Chain',
    'BackTrail' => 'BackTrail'
);

foreach($session_object as $var_name => $class_name ) {
    if ( !session_check($var_name) || !is_object(${$var_name}) ) {
        ${$var_name} = new ${class_name}();
        session_put($var_name);
    }
}
```

This means the shopping basket chain is created as a session-level runtime object and persists across the user lifecycle.

The class definition is in [inc/classes/Shopping_Basket_Chain.php](../inc/classes/Shopping_Basket_Chain.php).

The constructor calls:

```php
$this->_reload_data();
```

and `_reload_data()` reads current user context:

```php
$F = Framework::g_global();
$P = Person::g_global();
$this->id_client = (int)$P->data['id_client'];
$this->id_client_user = (int)$P->id;

if( $P->logged_in ) {
    $Basket_List = Data::get_basket_chain_basket_list( $this->id_client );
    if( $F->not_null($Basket_List) ) {
        $this->set_basket_list($Basket_List);
    } else {
        $this->init_basket();
    }
} else {
    $this->init_basket();
}
```

This is the first critical lifecycle point: the basket chain is loaded for the current client, or created if absent.

---

## 2. Basket chain and basket list

The basket chain holds a list of active baskets in `Basket_List` and tracks the current basket via `id_basket_current`.

Relevant methods in [inc/classes/Shopping_Basket_Chain.php](../inc/classes/Shopping_Basket_Chain.php):

- `add_basket()`
- `remove_basket()`
- `add_to_mainbasket()`
- `set_default_basket_by_date()`
- `init_basket()`
- `_check_valid_basket()`

The project supports multiple basket variants in the same chain, which matches the B2B multi-basket design described in the README.

The data source for the chain is `Data::get_basket_chain_basket_list()`, which resolves to the basket data repository in [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php):

```php
static function get_basket_chain_basket_list( $id_client ) {
    $query = 'select id_shopping_basket, id_client, description, date_create, date_modified,
    UNIX_TIMESTAMP(date_create) as ts_create, UNIX_TIMESTAMP(date_modified) as ts_modified,
    using_id_client_user, using_session_id, using_date, UNIX_TIMESTAMP(using_date) as ts_using,
    state
    from ' . TBL_SHOP_SHOPPING_BASKET . '
    where state != "ORDER" and id_client = ' . db_int($id_client) . '';
    $basket_list = db_result_array_full_id( db_query( $query ) );
    return $basket_list;
}
```

So the basket chain is a user-scoped aggregate built from persisted basket records.

---

## 3. Individual basket object

The actual basket entity is defined in [inc/classes/Shopping_Basket.php](../inc/classes/Shopping_Basket.php).

Its constructor accepts:

```php
function __construct($params = false, $create = false, $contents = false)
```

and uses the data supplied to hydrate the basket state:

```php
$this->reset();
self::$class = $this;

if( $params ) {
    $this->params = $params;
    if( $create ) {
        $new_params = $this->db_create_basket();
        $this->params['id_shopping_basket'] = $new_params['id_shopping_basket'];
        $this->params['id_shopping_basket_version'] = $new_params['id_shopping_basket_version'];
        ...
        $this->db_save_contents();
    } else {
        $this->id_shopping_basket=$this->params['id_shopping_basket'];
        $this->db_restore_contents();
        $this->calculate_total();
    }
}
```

This is the lifecycle boundary where a basket is either:

- created as a new basket instance
- restored from stored state and contents
- reloaded with totals and versions

### Basket properties

The basket stores:

- `contents`
- `product_info_array`
- `version`
- `history`
- `id_shopping_basket`
- `id_shopping_basket_version`
- `id_shopping_basket_history`
- `params`

The basket state is not just a list of products; it is a row plus audit/version/history metadata.

---

## 4. Basket contents and versioning

The basket is persisted in multiple related structures:

- basket main row: `TBL_SHOP_SHOPPING_BASKET`
- basket version rows: `TBL_SHOP_SHOPPING_BASKET_VERSION`
- basket product rows: `TBL_SHOP_SHOPPING_BASKET_PRODUCT`
- basket history rows: `TBL_SHOP_SHOPPING_BASKET_HISTORY`

The basket data class handles these transitions in [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php).

Important methods:

- `create_new_basket()`
- `put_basket_history()`
- `put_basket_info()`
- `put_basket_version_product_list()`
- `get_basket_version_list()`
- `get_basket_version_product_list()`
- `get_basket_history_list()`

The actual basket persistence flow is:

```text
Shopping_Basket::db_save_contents()
  -> Data::put_basket_version_product_list()
  -> stores product rows for the current basket version
```

and the state transitions produce new history entries:

```php
$this->params['state'] = 'ORDER';
return Data::put_basket_update_use_data( $this->params );
```

This means each meaningful basket state change is recorded as a history event.

---

## 5. Basket state machine

The basket is stateful.

Relevant state-change methods in [inc/classes/Shopping_Basket.php](../inc/classes/Shopping_Basket.php):

- `state_using_get()`
- `state_change_level()`
- `state_using_clear()`
- `state_lock_set()`
- `state_lock_unset()`
- `state_archive_order()`

The class uses values like:

- `USE_*`
- `FREE_*`
- `LOCK_*`
- `ORDER`

and updates the persisted `state` column using data methods such as:

```php
Data::put_basket_update_use_data( $this->params );
Data::put_basket_update_lock_data( $this->params );
```

These methods are implemented in [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php).

---

## 6. Basket-to-order conversion

The conversion point is reached when the basket is finalized as an order.

The critical call is in [inc/classes/Order.php](../inc/classes/Order.php):

```php
$this->source_basket = Shopping_Basket::get_order_data( (int)$this->data['id_shopping_basket'] );
```

and later:

```php
$Shopping_Basket->state_archive_order();
```

This shows that an order object can bind to the original basket and then archive the basket state as `ORDER`.

The method `Shopping_Basket::state_archive_order()` does the transition:

```php
function state_archive_order() {
    if( $this->check_rights('MAKE_ORDER') ) {
        $this->params['using_id_client_user'] = NULL;
        $this->params['using_session_id'] = NULL;
        $this->params['state'] = 'ORDER';

        return Data::put_basket_update_use_data( $this->params );
    } else {
        return false;
    }
}
```

This is the practical “order creation” step: the basket transitions from active shopping state to archived `ORDER` state.

---

## 7. Order object definition

The order entity is defined in [inc/classes/Order.php](../inc/classes/Order.php).

The constructor loads data by ID:

```php
function __construct( $id_order = 0, $mode = 'FULL',  $data = false) {
    $this->data = array();
    $this->product_list = array();
    $this->status_history = array();
    $this->set_mode( $mode );

    if( $id_order > 0 ) {
        return $this->load_data( (int)$id_order, $data );
    }
}
```

Then `load_data()` does:

```php
$this->id_order = $id_order;

if( $data ) $this->data = $data;
else  $this->data = Data::get_order_data( $id_order );

if( $F->not_null($this->data) ) {
    $this->product_list = Data::get_order_product_list( $id_order );
    if( $this->mode == 'FULL' ) $this->load_data_full();
    return sizeof($this->product_list);
} else {
    return false;
}
```

This is the order hydration pattern: order row + product list + full metadata.

---

## 8. Order data layer

The order data layer is implemented in [inc/classes/Data_Order.php](../inc/classes/Data_Order.php).

Key methods include:

- `get_order_data()`
- `get_order_product_list()`
- `get_order_attribute()`
- `get_address()`
- `put_order_*` and related order persistence methods

The basic relationship is:

```text
Order object -> Data::get_order_data() -> Data_Order::get_order_data() -> SQL result -> order payload
```

and then:

```text
Order product list -> Data::get_order_product_list() -> Data_Order::get_order_product_list() -> SQL rows -> product_list
```

This is how an order becomes a fully loaded business object instead of a single database row.

---

## 9. Complete lifecycle summary

The basket-to-order flow is:

```text
User session loads Shopping_Basket_Chain
  -> Data::get_basket_chain_basket_list()
  -> creates or restores Shopping_Basket instances
  -> basket contents are stored in basket version/product tables
  -> basket state changes are logged in basket history
  -> customer confirms order
  -> Shopping_Basket::state_archive_order() sets state = ORDER
  -> Order object is created with id_order
  -> Data::get_order_data() loads the order row
  -> Data::get_order_product_list() loads products
  -> order totals, addresses, attributes, and source basket are attached
  -> UI and business logic consume the final Order model
```

---

## 10. Reverse lookup: which basket created this order?

To answer the reverse-question “where did this order come from?” the trail is:

1. open [inc/classes/Order.php](../inc/classes/Order.php)
2. inspect `load_data()` and `source_basket`
3. find `Shopping_Basket::get_order_data()`
4. trace to [inc/classes/Shopping_Basket.php](../inc/classes/Shopping_Basket.php)
5. inspect `state_archive_order()` and the basket `params['state']`
6. then follow the data layer in [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php)

This gives a complete reverse map from order back to basket state and persisted basket records.

---

## 11. Practical maintenance guidance

When debugging basket or order behavior, follow this sequence:

1. identify which basket chain is active in the session
2. inspect the active `Shopping_Basket` instance and its `params['state']`
3. locate the matching database write/read methods in [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php)
4. trace the order conversion path in [inc/classes/Order.php](../inc/classes/Order.php)
5. validate against the persisted basket and order tables and their related product/history versions

This is the clearest route through the e-commerce state machine in this codebase.

---

## 12. Summary

The basket and order lifecycle is a persisted state machine built around two main entities:

- `Shopping_Basket` for active cart state and contents
- `Order` for finalized transactional data

The project stores basket state and content in dedicated tables, tracks historical changes, and then converts the active basket into an order object using the data access layer. The overall model is state-driven and database-backed rather than framework-driven or event-driven.
