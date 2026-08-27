# Object lifecycle, method definitions, and source usage map

This document connects the main object initializations, the methods that define their behavior, and the places where those objects are consumed later in the request lifecycle.

The project uses a legacy global bootstrap pattern rather than a container or DI system. The key idea is:

1. [config.php](../config.php) defines global constants and environment values.
2. [init.php](../init.php) includes the class files and creates the globally shared runtime objects.
3. Request-specific pages and modules create or reuse model objects such as `Article`, `Order`, `Product`, `Person`, and `Shop`.
4. Many classes use singleton-style static accessors such as `g_global()` and then call their methods via framework globals.

---

## 1. Bootstrap and global object initialization

### Entry points

- [init.php](../init.php)
- [SOAP/init.php](../SOAP/init.php)
- [2panel/admin/init.php](../2panel/admin/init.php)

The common bootstrap sequence is most visible in [init.php](../init.php):

```php
$F = new Framework();
$Page = new Page();
$Lang = new Lang();
$Rights = new Rights();

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

$Data = new Data();
```

### Definition sites for the main global objects

| Runtime object | Definition class | Initialisation site | Notes |
|---|---|---|---|
| `$F` | [inc/classes/Framework.php](../inc/classes/Framework.php) | [init.php](../init.php) | Main application request context; normalizes GET/POST and builds links |
| `$Page` | [inc/classes/Page.php](../inc/classes/Page.php) | [init.php](../init.php) | Layout manager for template blocks, CSS, JS, and head metadata |
| `$Lang` | [inc/classes/Lang.php](../inc/classes/Lang.php) | [init.php](../init.php) | Translation lookup and language loading |
| `$Rights` | [inc/classes/Rights.php](../inc/classes/Rights.php) | [init.php](../init.php) | Permission checks |
| `$Data` | [inc/classes/Data.php](../inc/classes/Data.php) | [init.php](../init.php) | Data repository facade for dynamic sub-classes |
| `$P` (Person) | [inc/classes/Person.php](../inc/classes/Person.php) | [init.php](../init.php) | Current user session object |
| `$Info` | [inc/classes/Info.php](../inc/classes/Info.php) | [init.php](../init.php) | Flash/info/status messages |
| `$Shopping_Basket_Chain` | [inc/classes/Shopping_Basket_Chain.php](../inc/classes/Shopping_Basket_Chain.php) | [init.php](../init.php) | Basket group/chain state |
| `$BackTrail` | [inc/classes/BackTrail.php](../inc/classes/BackTrail.php) | [init.php](../init.php) | Navigation/backtracking helper |

### Configuration loading

The app-wide constants are created in [config.php](../config.php), which reads [config.ini](../config.ini) and defines:

- database table names via `TBL_*`
- image paths via `IMAGE_*`
- routing components via `CFG_COM_*`
- directories via `DIR_INC_*`
- default app values via `DEFAULT_*`
- shop settings via `SHOP_*`
- HTTP asset paths like `DIR_WWW_CSS`, `DIR_WWW_JS`, `DIR_WWW_IMG`

This is the configuration root for all later runtime object behavior.

---

## 2. Core object definitions and how they are created

### Framework

Definition:
- [inc/classes/Framework.php](../inc/classes/Framework.php)

Constructor:
- `Framework::__construct()`
- assigns `GET`, `POST`, `REQUEST`, `RSA`, `form`, `virtualdir`
- calls `_request_normalize()`
- sets `self::$class = $this`

Singleton accessor:
- `Framework::g_global()`
- if the static singleton is missing, creates a `new Framework`

Reverse usage:
- Many classes read the current request context with `Framework::g_global()`
- Examples: [inc/classes/Shop.php](../inc/classes/Shop.php), [inc/classes/Page.php](../inc/classes/Page.php), [inc/classes/Lang.php](../inc/classes/Lang.php), [inc/classes/Order.php](../inc/classes/Order.php)

### Page

Definition:
- [inc/classes/Page.php](../inc/classes/Page.php)

Constructor:
- initializes template block storage and asset arrays

Important methods:
- `Page::start()` - sets framework pointer and loads template/component data
- `Page::set_raw_paths()` - maps CSS/JS/IMG directories
- `Page::add_css()` - adds stylesheet links
- `Page::put_js()` / `Page::put_css()` - renders asset blocks

Usage:
- [init.php](../init.php) creates the main `$Page` object
- Many modules set `$Page->head_title` and similar values before rendering
- Example: [inc/com/article.php](../inc/com/article.php)

### Data repository facade

Definition:
- [inc/classes/Data.php](../inc/classes/Data.php)

Important behavior:
- `Data` extends `Data_Person` and acts as a repository/factory facade
- `Data::__call()` dynamically loads a concrete data class such as `Data_Article`, `Data_Product`, `Data_Order`, etc.

Example from the class:

```php
include_once('Data_' . $class_name . '.php');
eval('$this->ac[$class_name] = new Data_' . $class_name . '();');
```

This is the main dynamic factory pattern in the project and explains why many domain entities are represented through specialist data classes rather than explicit interfaces.

### Article flow

Definition:
- [inc/classes/Article.php](../inc/classes/Article.php)
- data access class: [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)

Call chain:

1. [inc/com/article.php](../inc/com/article.php)
   ```php
   $Article = new Article($F->GET['key']);
   ```
2. `Article::__construct()` calls `load_article()`
3. `load_article()` calls `Data::get_article($key)`
4. `Data_Article::get_article($key)` executes the SQL and returns the article row
5. `$Article->param` contains the loaded values for the template

The article page then renders:
- `$Article->param['title']`
- `$Article->param['content']`

This is a good concrete example of the “definition -> initialization -> data lookup -> rendering” pattern.

---

## 3. Method-definition to usage mapping

### Framework methods used globally

| Method | Definition | Typical use sites |
|---|---|---|
| `Framework::g_global()` | [inc/classes/Framework.php](../inc/classes/Framework.php) | Many model classes, layout code |
| `Framework::make_link()` | [inc/classes/Framework.php](../inc/classes/Framework.php) | Page renderers and redirects |
| `Framework::redirect()` | [inc/classes/Framework.php](../inc/classes/Framework.php) | Page-level navigation and error flows |
| `Framework::not_null()` / `is_null()` | [inc/classes/Framework.php](../inc/classes/Framework.php) | Validation and guard expressions |

Common usage pattern:

```php
$F = Framework::g_global();
```

This is used across the codebase to access the current request object without passing it as a constructor dependency.

### Page methods used by components

| Method | Definition | Typical use sites |
|---|---|---|
| `Page::g_global()` | [inc/classes/Page.php](../inc/classes/Page.php) | Accessing the current layout instance |
| `Page::start()` | [inc/classes/Page.php](../inc/classes/Page.php) | Initial page bootstrap |
| `Page::set_raw_paths()` | [inc/classes/Page.php](../inc/classes/Page.php) | Asset path resolution |
| `Page::add_css()` | [inc/classes/Page.php](../inc/classes/Page.php) | Template style injection |
| `Page::put_head_title()` | [inc/classes/Page.php](../inc/classes/Page.php) | Setting page metadata |

### Singleton-like model objects

| Class | Global accessor | Definition |
|---|---|---|
| `Article` | `Article::g_global()` | [inc/classes/Article.php](../inc/classes/Article.php) |
| `Shop` | `Shop::g_global()` | [inc/classes/Shop.php](../inc/classes/Shop.php) |
| `Person` | `Person::g_global()` | [inc/classes/Person.php](../inc/classes/Person.php) |
| `Page` | `Page::g_global()` | [inc/classes/Page.php](../inc/classes/Page.php) |
| `Framework` | `Framework::g_global()` | [inc/classes/Framework.php](../inc/classes/Framework.php) |
| `SplitPage` | `SplitPage::g_global()` | [inc/classes/SplitPage.php](../inc/classes/SplitPage.php) |

This pattern explains why many modules can be written with only a few global references instead of explicit constructor injection.

---

## 4. Reverse lookup: “what uses this object?”

### Reverse map for the main global objects

#### Framework

Created in:
- [init.php](../init.php)

Used in:
- [inc/classes/Shop.php](../inc/classes/Shop.php)
- [inc/classes/Page.php](../inc/classes/Page.php)
- [inc/classes/Lang.php](../inc/classes/Lang.php)
- [inc/classes/Order.php](../inc/classes/Order.php)
- [inc/classes/Data_Product.php](../inc/classes/Data_Product.php)
- [inc/classes/Data_Person.php](../inc/classes/Data_Person.php)
- [inc/classes/Data_Order.php](../inc/classes/Data_Order.php)
- [inc/classes/Shopping_Basket.php](../inc/classes/Shopping_Basket.php)

#### Page

Created in:
- [init.php](../init.php)

Used in:
- [inc/com/article.php](../inc/com/article.php)
- [inc/com/main.php](../inc/com/main.php)
- [inc/com/catalog.php](../inc/com/catalog.php)
- [inc/mod/basket_list.php](../inc/mod/basket_list.php)
- [inc/classes/Page.php](../inc/classes/Page.php) itself

#### Data

Created in:
- [init.php](../init.php)

Used by:
- [inc/classes/Article.php](../inc/classes/Article.php)
- [inc/classes/Shop.php](../inc/classes/Shop.php)
- many `Data_*` classes with dynamic lookup/wrappers
- example: [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)

#### Shop

Created indirectly in [init.php](../init.php) by session bootstrap pattern and accessed by:
- [inc/mod/currency_exchange_m.php](../inc/mod/currency_exchange_m.php)
- [inc/mod/currency_exchange.php](../inc/mod/currency_exchange.php)
- [inc/classes/Shop.php](../inc/classes/Shop.php)

#### Article

Created in:
- [inc/com/article.php](../inc/com/article.php)
- [inc/com/contact.php](../inc/com/contact.php)
- [inc/com/main.php](../inc/com/main.php)

Looked up via:
- [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)

---

## 5. Concrete object lifecycle examples

### Example A: page bootstrap

Flow:
- [init.php](../init.php) creates the runtime globals
- [inc/classes/Page.php](../inc/classes/Page.php) initializes template state and asset paths
- page modules assign headers and templates
- final output is rendered by the page engine

Relevant files:
- [init.php](../init.php)
- [inc/classes/Page.php](../inc/classes/Page.php)
- [inc/com/article.php](../inc/com/article.php)
- [inc/com/main.php](../inc/com/main.php)

### Example B: article page rendering

Flow:
- [inc/com/article.php](../inc/com/article.php) creates `new Article($F->GET['key'])`
- [inc/classes/Article.php](../inc/classes/Article.php) loads data with `Data::get_article()`
- [inc/classes/Data_Article.php](../inc/classes/Data_Article.php) runs SQL and returns a row
- template consumes `$Article->param['title']` and `$Article->param['content']`

### Example C: shop attribute lookup

Flow:
- [inc/classes/Shop.php](../inc/classes/Shop.php) holds the singleton instance
- `Shop::get_shop_attribute($name)` resolves attribute values
- if not loaded, it fills `attributes` from `Data::get_shop_attributes()`
- the result is read by modules using the global shop instance

---

## 6. Reverse information: “what is defined here, and where is it used?”

This repository is highly global-state-driven, so a useful reverse lookup is:

- Start from the class definition file
- Check the singleton accessor (`g_global()`) and constructor
- Find the places that call it, usually with `Framework::g_global()` or `$Class::g_global()`
- Then inspect the rendering/files that instantiate the class directly (`new Class(...)`)

Examples:

- `Framework`
  - Defined in [inc/classes/Framework.php](../inc/classes/Framework.php)
  - Instantiated in [init.php](../init.php)
  - Reused globally across many classes

- `Article`
  - Defined in [inc/classes/Article.php](../inc/classes/Article.php)
  - Instantiated in [inc/com/article.php](../inc/com/article.php)
  - Data comes from [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)

- `Page`
  - Defined in [inc/classes/Page.php](../inc/classes/Page.php)
  - Instantiated in [init.php](../init.php)
  - Used widely in template and component rendering

- `Shop`
  - Defined in [inc/classes/Shop.php](../inc/classes/Shop.php)
  - Accessed through `Shop::g_global()`
  - Relevant modules: [inc/mod/currency_exchange.php](../inc/mod/currency_exchange.php), [inc/mod/currency_exchange_m.php](../inc/mod/currency_exchange_m.php)

---

## 7. Practical maintenance guidance

When tracing a bug in this codebase, follow this order:

1. Find the entry file that boots the request: [init.php](../init.php)
2. Identify the global instance being created there
3. Find the class definition and its `g_global()` or constructor
4. Trace the method call to the relevant `Data_*` class or template file
5. Reverse-check the component page that instantiates the object

This is the most reliable way to understand the app without a full framework runtime.

---

## 8. Summary

The project relies on a classic legacy PHP design:

- global configuration in [config.php](../config.php)
- global bootstrap in [init.php](../init.php)
- singleton-like classes with static accessors such as `g_global()`
- data access through `Data` and `Data_*` classes
- component pages that instantiate domain objects (`new Article`, `new Order`, etc.)
- template rendering through the `Page` class and its layout blocks

This pattern is relatively simple to trace once you know the bootstrap chain and the singleton conventions.
