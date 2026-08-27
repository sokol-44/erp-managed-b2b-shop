# Project Rationale

## Background & Objectives

This project was originally developed to support a specialized company department by building upon Microsoft-based technologies centered around a Microsoft SQL Server (MSSQL) database core.

Designed as an extension to co-exist with existing applications operating on the same central database, its primary objective was to enable legacy ERP and warehouse management systems (such as WfMAG) to conduct full-scale e-commerce operations via an external web server.

Under the initial architectural assumptions, the system was designed without any web-based administration panel; all content and operational data were intended to be ingested and managed strictly via a SOAP API.

## Database-Driven Business Logic & Architecture

To satisfy an additional requirement of duplicating SOAP API communication via direct database operations, key responsibilities were offloaded to the MSSQL database layer:

* **Binary Media Storage:** Storing product images directly within the database.
* **Database-Level Security:** Encrypting and hashing user passwords directly inside the database engines.
* **Stored Procedure Abstraction:** Implementing a dedicated set of stored procedures for all CRUD operations, including integrated error handling and explicit execution status codes.

Because the core business logic was architected within the Microsoft ecosystem using visual management tools, advanced data structures were embedded directly into the database schema despite the absence of a traditional web interface.

## Advanced & Unique Features

For its time, the system introduced several sophisticated e-commerce capabilities rare for standard B2B applications:

* **Advanced Cart Management:**
  * Support for multiple active carts per account.
  * Cart switching, deletion, duplication, and merging.
  * Full cart historical audit trails.
* **Order Tracking & Auditing:** Comprehensive order histories complete with timestamps and state descriptions.
* **Flexible Product Catalog:** Support for complex product variants and subtypes.
* **Multi-Tenant Account Structures:** Hierarchical customer accounts allowing a single client entity to manage multiple sub-users.
* **Sales Representative Assignment:** Granular mapping of dedicated sales reps at either the client or individual user level.
* **External Integration Hooks:** Reserved custom payload fields on orders designed exclusively for downstream external systems.
* **Extensible Dynamic Attributes:** An extensible key-value attribute architecture supporting custom metadata across:
  * Orders
  * Clients
  * Users
  * Products
  * Attribute Groups
