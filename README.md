# erp-managed-b2b-shop

A custom-built B2B E-commerce engine originally architected in 2010-2014. Designed for specialized business workflows, this project uses direct database control and ERP/warehouse systems integration over heavy framework abstractions.

## Overview

The `erp-managed-b2b-shop` is a B2B solution developed to function as a specialized extension of an ERP system. Unlike traditional e-commerce platforms, this system intentionally omits a web-based administration panel; all content, product catalogs, pricing, and inventory management are handled externally via a **SOAP API**, limiting posibilities of desynchronisation between shop and the current state of the warehouse management system/ERP.

## Key Features

*   **API-Driven Management:** The system is purely transactional; all administration is performed remotely by the ERP, ensuring total data consistency.
*   **Multi-Basket Engine:** Features a session management system that allows users to maintain, switch between, and store "favorite" baskets simultaneously.
*   **Virtual Directory Support:** Supports multi-tenancy configurations, allowing multiple shop instances to run on the same codebase with unique parameters and local settings.
*   **Custom Templating System:** Includes lightweight engine for rendering web interfaces and transactional email communications.
*   **Joomla-Inspired Design:** Built upon modular architecture patterns common in early 2010s PHP development.

## Technical Specifications

*   **Language:** PHP 5.x
*   **Database:** MySQL (Primary); experimental support for PostgreSQL
*   **Integration:** SOAP API
*   **Schema Management:** Full database architecture documented via MySQL Workbench
*   **License:** AGPL-3.0

## Database & Infrastructure

The database schema has been designed to handle relational B2B data. Source files for the schema, including the original MySQL Workbench models, are located in the `/Models` directory.

*Note: While the primary implementation targets MySQL, the data access layer was designed with potential PostgreSQL compatibility in mind.*

## Status & Future Development

This project represents a piece of my professional history. While it has not seen active maintenance for approximately ten years, I am currently in the process of reviving it as a showcase of my foundational engineering work.

As of 2026, my roadmap for this repository includes:
*   **Documentation Overhaul:** Comprehensive restructuring and expansion of the documentation to make the codebase accessible and understandable for modern developers.
*   **Translate:** Most of the code is in English, but there is some Polish comments and variable naming.
*   **Codebase Archiving:** Cleaning up the existing codebase and ensuring its integrity.
*   **AI-Assisted Revitalization:** I am leveraging modern AI tools to accelerate the documentation and refactoring processes. To ensure high-quality standards, every AI-generated contribution is carefully verified, tested, and curated to maintain the architectural intent of the original system.

## Architecture Notes

A set of architecture-focused reference documents has been added under the [doc](doc) folder to help map the legacy runtime flow and file relationships.

### Included documentation

*   [doc/README.md](doc/README.md) — documentation index and reading order
*   [doc/object-flow-and-call-map.md](doc/object-flow-and-call-map.md) — main object initialization and reverse-call map
*   [doc/data-layer-mapping.md](doc/data-layer-mapping.md) — mapping between domain objects, `Data` facade, and the concrete `Data_*` files
*   [doc/request-template-class-flow.md](doc/request-template-class-flow.md) — request-to-page-to-class-to-template tracing for a representative page type
*   [doc/basket-order-lifecycle.md](doc/basket-order-lifecycle.md) — basket state transitions and order creation lifecycle
*   [doc/design-patterns.md](doc/design-patterns.md) — summary of the project’s main design patterns and how they are implemented in code
*   [doc/rationale.md](doc/rationale.md) — project background, business intent, and the original ERP-first design rationale

These notes were created to document the actual runtime structure of the project rather than to describe an idealized modern architecture. They are especially useful when tracing how request values, global singletons, and database access are connected in this legacy PHP codebase, and why the system was built around ERP-driven data ownership, catalog synchronization, and SOAP-mediated integration.

## License

This project is licensed under the **AGPL-3.0 License**. See the [LICENSE](LICENSE) file for details.