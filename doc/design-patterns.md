# Design patterns used in the project

This document maps the main design patterns that appear across the repository and connects each one to the concrete files that implement it. The goal is to explain both the architectural intent and the practical functionality delivered in this legacy PHP codebase.

> Note: these patterns are implemented in a historical, global-state-heavy way. The code often approximates the idea of a modern pattern without fully adopting modern framework conventions.

---

## 1. Singleton / global registry pattern

### Purpose

The project uses singleton-like static accessors to keep one global instance of key runtime objects and make them available from many places without constructor injection.

### Files

- [inc/classes/Framework.php](../inc/classes/Framework.php)
- [inc/classes/Page.php](../inc/classes/Page.php)
- [inc/classes/Lang.php](../inc/classes/Lang.php)
- [inc/classes/Person.php](../inc/classes/Person.php)
- [inc/classes/Shop.php](../inc/classes/Shop.php)
- [inc/classes/Article.php](../inc/classes/Article.php)
- [inc/classes/Price.php](../inc/classes/Price.php)
- [inc/classes/Rights.php](../inc/classes/Rights.php)
- [inc/classes/Info.php](../inc/classes/Info.php)
- [inc/classes/Shopping_Basket_Chain.php](../inc/classes/Shopping_Basket_Chain.php)

### What it provides

- a single shared application context for request processing
- direct access to the current user, page renderer, translations, and rights state
- session-wide persistence of runtime objects across request steps
- easy access patterns like `Framework::g_global()` and `Page::g_global()`

### In practice

The closest pattern to a real singleton is used for:

- request/route context (`Framework`)
- page rendering state (`Page`)
- dictionary of translations (`Lang`)
- active user/session (`Person`)
- current shop configuration (`Shop`)
- basket chain state (`Shopping_Basket_Chain`)

This is the core structural pattern behind most of the object lifecycle described in the other architecture docs.

---

## 2. Repository / facade pattern

### Purpose

The project centralizes data access through a facade and repository-like classes instead of allowing each view or domain object to query the database directly.

### Files

- [inc/classes/Data.php](../inc/classes/Data.php)
- [inc/classes/Data_Article.php](../inc/classes/Data_Article.php)
- [inc/classes/Data_Product.php](../inc/classes/Data_Product.php)
- [inc/classes/Data_Order.php](../inc/classes/Data_Order.php)
- [inc/classes/Data_Person.php](../inc/classes/Data_Person.php)
- [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php)
- [inc/classes/Data_Shop.php](../inc/classes/Data_Shop.php)
- [inc/functions/database.php](../inc/functions/database.php)

### What it provides

- centralized access to domain-specific persistence logic
- dynamic class resolution for `Data_*` classes
- a single entry point for data retrieval and persistence
- separation between business object usage and low-level SQL execution

### In practice

`Data` acts like a repository facade. It resolves calls such as `Data::get_article(...)` or `Data::get_order_data(...)` into the matching specialized repository file. This keeps domain objects such as `Article` and `Order` focused on runtime behavior instead of raw SQL.

---

## 3. Entity / domain object pattern

### Purpose

The system models concrete business objects as entity-like classes that carry state and provide methods for validation, totals, and loading logic.

### Files

- [inc/classes/Article.php](../inc/classes/Article.php)
- [inc/classes/Order.php](../inc/classes/Order.php)
- [inc/classes/Product.php](../inc/classes/Product.php)
- [inc/classes/Person.php](../inc/classes/Person.php)
- [inc/classes/Shop.php](../inc/classes/Shop.php)
- [inc/classes/Shopping_Basket.php](../inc/classes/Shopping_Basket.php)
- [inc/classes/Invoice.php](../inc/classes/Invoice.php)

### What it provides

- object wrappers for rows loaded from the database
- business behavior attached to the object itself
- fields such as `data`, `product_list`, `attributes`, `params`, and `contents`
- convenience methods for rights checks, totals, and lifecycle transitions

### In practice

Examples:

- `Article` loads and stores loaded content data for page rendering
- `Order` calculates totals, loads product list, and knows its address and attributes
- `Shopping_Basket` tracks contents, versions, state, and state transitions
- `Product` is used as a product entity abstraction in catalog and pricing workflows

This pattern is the main business-layer representation in the app.

---

## 4. State pattern

### Purpose

The project explicitly models lifecycle states for baskets, rights, and some order-related transitions.

### Files

- [inc/classes/Shopping_Basket.php](../inc/classes/Shopping_Basket.php)
- [inc/classes/Rights.php](../inc/classes/Rights.php)
- [inc/classes/Data_Basket.php](../inc/classes/Data_Basket.php)
- [inc/classes/Order.php](../inc/classes/Order.php)

### What it provides

- a state machine for basket lifecycle (`USE`, `FREE`, `LOCK`, `ORDER`)
- transitions such as state changes, lock/unlock, usage tracking, and archival
- rights parsing and role-level validation via `Rights::split_state()`
- behavior that depends on current state instead of static rules only

### In practice

`Shopping_Basket` uses methods like:

- `state_using_get()`
- `state_change_level()`
- `state_lock_set()`
- `state_archive_order()`

This gives the basket a clear lifecycle and allows the system to decide whether a user may modify a basket or finalize it as an order.

---

## 5. Factory pattern

### Purpose

Several classes build other objects or create domain outputs dynamically instead of hardcoding every case.

### Files

- [inc/classes/Html.php](../inc/classes/Html.php)
- [inc/classes/Mail.php](../inc/classes/Mail.php)
- [inc/classes/Data.php](../inc/classes/Data.php)
- [inc/classes/Soap_Server_worker.php](../inc/classes/Soap_Server_worker.php)

### What it provides

- dynamic creation of HTML elements or managers
- object creation decisions based on runtime data
- decoupling of common construction logic from concrete implementations

### In practice

`Html` is described as a factory-like class, and `Data` dynamically resolves a concrete `Data_*` class when a method is invoked. `Mail` also groups mail creation behavior and is a good example of a factory/adapter combination.

---

## 6. Strategy / adapter pattern

### Purpose

The system contains classes that switch behavior or adapt external interfaces depending on the execution mode or target system.

### Files

- [inc/classes/Mail2Send.php](../inc/classes/Mail2Send.php)
- [inc/classes/Soap_Server.php](../inc/classes/Soap_Server.php)
- [inc/classes/SplitPage.php](../inc/classes/SplitPage.php)
- [inc/classes/Mail.php](../inc/classes/Mail.php)

### What it provides

- selection of different rendering or processing behaviors at runtime
- compatibility layers around mail delivery or SOAP invocation
- page-splitting or page-view strategy behavior without one-off logic in controllers

### In practice

`Mail2Send` is explicitly described as a strategy class, while `Soap_Server` is documented as a strategy/singleton combination. `SplitPage` is also tagged as strategy/facade, which fits the page listing and partition logic used for lists and pagination.

---

## 7. Facade pattern

### Purpose

A facade reduces direct complexity by exposing a simpler interface to a larger subsystem.

### Files

- [inc/classes/Data.php](../inc/classes/Data.php)
- [inc/classes/SplitPage.php](../inc/classes/SplitPage.php)
- [inc/classes/Framework.php](../inc/classes/Framework.php)
- [inc/classes/Html.php](../inc/classes/Html.php)

### What it provides

- a simpler system-level interface for request normalization and page composition
- reduced knowledge required by the rest of the code to interact with complex subsystems
- a central object for common operations such as query resolution, request normalization, and view rendering

### In practice

`Data` is the clearest facade: it hides the details of the many `Data_*` classes and serves as the primary data access interface. `SplitPage` also feels like a facade around list/pagination logic and page rendering.

---

## 8. Proxy / decorator-like wrappers

### Purpose

Some classes act as frontends or wrappers around other objects, especially when they add state, rights, or a user-specific view.

### Files

- [inc/classes/Shopping_Basket_Chain.php](../inc/classes/Shopping_Basket_Chain.php)
- [inc/classes/Soap_Server_worker.php](../inc/classes/Soap_Server_worker.php)
- [inc/classes/Order_Chain.php](../inc/classes/Order_Chain.php)
- [inc/classes/Order_History.php](../inc/classes/Order_History.php)

### What it provides

- aggregated access to a collection of underlying objects
- interface-level control over a list of baskets or orders
- a middle layer between low-level entities and the calling code

### In practice

`Shopping_Basket_Chain` acts as a managed container over many basket instances, while `Order_Chain` is a collection wrapper for multiple order entities.

---

## 9. Worker / service pattern

### Purpose

The SOAP layer uses worker-like classes to perform data transformations and intensive processing tasks, isolating the heavy logic from the API boundary.

### Files

- [inc/classes/Soap_Server_worker.php](../inc/classes/Soap_Server_worker.php)
- [inc/classes/Soap_Server.php](../inc/classes/Soap_Server.php)
- [inc/classes/Soap_Server_class.php](../inc/classes/Soap_Server_class.php)

### What it provides

- a dedicated layer for processing business data and API payloads
- transformation from external SOAP input into internal data structures
- separation of network/API concerns from business/domain processing

### In practice

`Soap_Server_worker` prepares and converts SOAP request data, validates it, and pushes it into structured internal objects like `ClientData`, `ProductData`, and related classes.

---

## 10. Entity list / collection pattern

### Purpose

The code contains list-oriented wrappers for grouping entities by type or by relationship.

### Files

- [inc/classes/Order_Chain.php](../inc/classes/Order_Chain.php)
- [inc/classes/Order_History.php](../inc/classes/Order_History.php)
- [inc/classes/Shopping_Basket_Chain.php](../inc/classes/Shopping_Basket_Chain.php)

### What it provides

- grouped access to many business objects
- collection logic for baskets, orders, and histories
- easier bulk operations such as load, merge, or remove actions

### In practice

These classes provide a managed collection around a domain entity type and help maintain consistent state and operations for each value in the group.

---

## 11. Summary of the architectural pattern set

The project blends several patterns together in a legacy style:

- Singleton / global registry for application and session state
- Repository / facade for data access
- Entity for product, order, basket, and user business objects
- State for basket and rights transitions
- Factory and strategy for dynamic object creation and processing modes
- Proxy-like wrappers and collection classes for grouped business objects
- Worker/service classes for SOAP integration

These patterns are implemented with global state and static methods rather than modern dependency injection, but the underlying ideas are clearly present and are central to understanding the codebase.

---

## 12. Recommended reading path

To understand these patterns in context, read:

1. [object-flow-and-call-map.md](object-flow-and-call-map.md)
2. [data-layer-mapping.md](data-layer-mapping.md)
3. [basket-order-lifecycle.md](basket-order-lifecycle.md)
4. [request-template-class-flow.md](request-template-class-flow.md)

This gives a practical map of how the patterns are applied in the runtime flow rather than only in class comments.
