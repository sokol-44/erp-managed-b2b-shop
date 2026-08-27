# Project architecture documentation

This directory collects the core architecture notes for the legacy B2B storefront.

## Index

### Core object and lifecycle maps
- [object-flow-and-call-map.md](object-flow-and-call-map.md) — overview of object creation, singleton/global runtime usage, and reverse lookup patterns
- [data-layer-mapping.md](data-layer-mapping.md) — how domain objects connect to the `Data` facade and the dedicated `Data_*` classes
- [request-template-class-flow.md](request-template-class-flow.md) — example of a request flowing from a page handler to a domain class and final template rendering
- [basket-order-lifecycle.md](basket-order-lifecycle.md) — flow from basket state management through final order creation and persistence
- [design-patterns.md](design-patterns.md) — summary of the project’s main design patterns, their intent, and the files implementing them
- [rationale.md](rationale.md) — history, business context, architecture intent, and the reasons behind the original ERP-first design

## Reading order

For a first pass, read these in order:

1. [rationale.md](rationale.md)
2. [object-flow-and-call-map.md](object-flow-and-call-map.md)
3. [data-layer-mapping.md](data-layer-mapping.md)
4. [request-template-class-flow.md](request-template-class-flow.md)
5. [basket-order-lifecycle.md](basket-order-lifecycle.md)
6. [design-patterns.md](design-patterns.md)

## Purpose

These documents are meant to make the legacy system easier to understand without rewriting it. They focus on real runtime connections, file relationships, and the historical global-state patterns used throughout the codebase.
