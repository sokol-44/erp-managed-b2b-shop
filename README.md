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

## License

This project is licensed under the **AGPL-3.0 License**. See the [LICENSE](LICENSE) file for details.