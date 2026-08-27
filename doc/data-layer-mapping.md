# Data layer mapping

This document explains how the project maps requests, model objects, and database access logic across the legacy PHP storefront.

The design is not a modern ORM or repository framework. Instead, the project follows a pattern of:

- a global bootstrap that creates runtime objects
- a `Data` facade that dynamically resolves domain-specific data classes
- `Data_*` classes that encapsulate SQL access for each entity
- domain objects such as `Article`, `Order`, `Product`, `Person`, and `Shop` that load and hold the data

---

## 1. High-level architecture

### Core layers

1. Request bootstrap
   - [config.php](../config.php)
   - [init.php](../init.php)
   - [SOAP/init.php](../SOAP/init.php)

2. Global runtime container
   - [inc/classes/Framework.php](../inc/classes/Framework.php)
   - [inc/classes/Page.php](../inc/classes/Page.php)
   - [inc/classes/Lang.php](../inc/classes/Lang.php)
   - [inc/classes/Rights.php](../inc/classes/Rights.php)

3. Domain model layer
   - [inc/classes/Article.php](../inc/classes/Article.php)
   - [inc/classes/Order.php](../inc/classes/Order.php)
   - [inc/classes/Product.php](../inc/classes/Product.php)
   - [inc/classes/Person.php](../inc/classes/Person.php)
   - [inc/classes/Shop.php](../inc/classes/Shop.php)

4. Data access layer
   - [inc/classes/Data.php](../inc/classes/Data.php)
   - [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)
   - [inc/classes/Data_Order.php](../inc/classes/Data_Order.php)
   - [inc/classes/Data_Product.php](../inc/classes/Data_Product.php)
   - [inc/classes/Data_Person.php](../inc/classes/Data_Person.php)
   - [inc/classes/Data_Shop.php](../inc/classes/Data_Shop.php)

5. Database utility layer
   - [inc/functions/database.php](../inc/functions/database.php)
   - [inc/functions/global.php](../inc/functions/global.php)

---

## 2. Bootstrap and data initialization

The global bootstrap in [init.php](../init.php) does the following:

```php
include(DIR_INC_CLASSES . DS . 'Data_Contact.php');
include(DIR_INC_CLASSES . DS . 'Data_Article.php');
include(DIR_INC_CLASSES . DS . 'Data_Picture.php');
include(DIR_INC_CLASSES . DS . 'Data_Shop.php');
include(DIR_INC_CLASSES . DS . 'Data_Order.php');
include(DIR_INC_CLASSES . DS . 'Data_Basket.php');
include(DIR_INC_CLASSES . DS . 'Data_Product.php');
include(DIR_INC_CLASSES . DS . 'Data_Category.php');
include(DIR_INC_CLASSES . DS . 'Data_Rights.php');
include(DIR_INC_CLASSES . DS . 'Data_Person.php');
include(DIR_INC_CLASSES . DS . 'Data.php');
```

After that, the runtime creates the main shared objects:

```php
$F = new Framework();
$Page = new Page();
$Lang = new Lang();
$Rights = new Rights();
$Data = new Data();
```

This means the object graph is globally accessible to the rest of the request instead of being passed via constructor injection.

---

## 3. The central Data facade

The key class is [inc/classes/Data.php](../inc/classes/Data.php).

This class acts as a facade and dynamic dispatcher:

- it extends a domain-specific base like `Data_Person`
- it exposes many static methods for fetching records
- it resolves domain-specific subclasses dynamically
- it loads a `Data_*` class only when a method is called that is not already initialized

The central factory pattern is in `Data::__call()`:

```php
$name_array = explode('_', $name);
$class_name = self::get_legalsubclass($name_array['1']);

if( !isset($this->ac[$class_name]) || !is_object($this->ac[$class_name]) ) {
   include_once('Data_' . $class_name . '.php');
   eval('$this->ac[$class_name] = new Data_' . $class_name . '();');
   $this->ac[$class_name]->$name($arguments);
} else {
   $this->ac[$class_name]->$name($arguments);
}
```

### Why this matters

This is the project’s main data abstraction layer. It bridges:

- a domain object call (`Article`, `Product`, `Person`, etc.)
- to a runtime data object (`Data_Article`, `Data_Product`, `Data_Person`)
- to SQL execution via the database helper functions in [inc/functions/database.php](../inc/functions/database.php)

---

## 4. Data class pattern by entity

### Entity boundary pattern

The project follows a strong naming convention for data classes:

| Domain object | Data class | Definition |
|---|---|---|
| `Article` | `Data_Article` | [inc/classes/Data_Article.php](../inc/classes/Data_Article.php) |
| `Product` | `Data_Product` | [inc/classes/Data_Product.php](../inc/classes/Data_Product.php) |
| `Order` | `Data_Order` | [inc/classes/Data_Order.php](../inc/classes/Data_Order.php) |
| `Person` | `Data_Person` | [inc/classes/Data_Person.php](../inc/classes/Data_Person.php) |
| `Shop` | `Data_Shop` | [inc/classes/Data_Shop.php](../inc/classes/Data_Shop.php) |
| `Contact` | `Data_Contact` | [inc/classes/Data_Contact.php](../inc/classes/Data_Contact.php) |
| `Category` | `Data_Category` | [inc/classes/Data_Category.php](../inc/classes/Data_Category.php) |

These classes usually expose static methods such as:

- `get_article($key)`
- `get_product(...)`
- `get_order(...)`
- `get_person(...)`
- `get_shop_attributes()`

and execute raw SQL queries with the helper functions from [inc/functions/database.php](../inc/functions/database.php).

---

## 5. Concrete data flow example: Article

### Definitions involved

- domain object: [inc/classes/Article.php](../inc/classes/Article.php)
- data object: [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)
- request page: [inc/com/article.php](../inc/com/article.php)

### Actual flow

1. Request page loads the article object:

```php
$Article = new Article($F->GET['key']);
```

2. `Article::__construct()` calls:

```php
$this->load_article($key);
```

3. `load_article()` calls:

```php
$this->param = Data::get_article($key);
```

4. `Data::get_article()` is a dynamic call resolved through `Data` facade logic and lands in `Data_Article::get_article()`.

5. `Data_Article::get_article()` runs a SQL query:

```php
$query = 'select a.id_article, a.date_created, a.date_modified, a.title, a.content
from ' . TBL_GLOBAL_ARTICLE . ' a
where a.id_article = ' . db_int($key) . '';
$result = db_query($query);
return db_fetch_array($result);
```

6. Returned row is stored in `$Article->param` and consumed by the page template.

This is the clearest example of the system’s data architecture: the domain object asks the global data layer for a record, and the data layer resolves the proper SQL implementation.

---

## 6. Data access helper layer

The SQL helper functions are defined in [inc/functions/database.php](../inc/functions/database.php).

Typical functions include:

- `db_init()`
- `db_query()`
- `db_fetch_array()`
- `db_result_array()`
- `db_rows()`
- `db_escape()`
- `db_int()`

These are procedural wrappers around the database connection and query execution. The data classes do not directly connect with PDO or a framework DBAL layer; they call these helper functions instead.

That is why the system is easy to trace but tightly coupled to legacy procedural globals.

---

## 7. Reverse mapping: “what data class backs this domain object?”

| Domain object | Data class used | Typical data method |
|---|---|---|
| `Article` | `Data_Article` | `get_article()` |
| `Product` | `Data_Product` | `get_product()` / list methods |
| `Order` | `Data_Order` | `get_order()` / `get_orders()` |
| `Person` | `Data_Person` | `get_person()` / login functions |
| `Shop` | `Data_Shop` | `get_shop_attributes()` |
| `Category` | `Data_Category` | `get_category()` / list methods |

This is the core reverse map: the object visible in templates is usually supported by a matching `Data_*` file behind it.

---

## 8. Naming pattern and file relationship

The pattern is very consistent:

- a business or domain object class file: `inc/classes/Article.php`
- matching database access file: `inc/classes/Data_Article.php`
- request page or module: `inc/com/article.php`

The relationship is effectively:

```text
request/page -> domain object -> Data facade -> Data_* class -> database helpers -> SQL result -> domain object property
```

---

## 9. Practical maintenance rules

When modifying the data layer, follow this order:

1. find the object creation in the page or module
2. locate the corresponding domain class definition
3. trace the call into `Data::*` or `Data_*::*`
4. inspect the exact SQL query in the matching `Data_*` file
5. validate against the helper functions and table constants from [config.php](../config.php)

This is the most reliable way to understand the legacy data architecture without a formal framework map.

---

## 10. Summary

The data layer is centered around a dynamic facade pattern:

- [inc/classes/Data.php](../inc/classes/Data.php) provides the runtime entry point
- each `Data_*` class contains the concrete SQL logic for one domain
- domain objects such as `Article` or `Order` pull that data into runtime properties
- the database access itself sits behind legacy procedural helper functions in [inc/functions/database.php](../inc/functions/database.php)

This keeps the application relatively direct and predictable, but it is tightly coupled to global state and naming conventions rather than modern dependency wiring.
