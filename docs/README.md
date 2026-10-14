# Documentation

Welcome to the Netmex HydratorBundle documentation.

Netmex HydratorBundle helps you convert input arrays into PHP objects through reusable mapper definitions. Mapping definitions can specify field transformations, validation constraints, target models, and nested mappings.

## Guides

### Getting started

- [Installation and configuration](installation.md)
- [Basic usage](basic-usage.md)
- [Mapper options](mapper-options.md)

### Features

- [Transformers](transformers.md)
- [Validation](validation.md)
- [Nested and recursive mapping](nested-and-recursive-mapping.md)
- [Collection hydration](collections.md)
- [Hydrating existing objects](existing-objects.md)
- [Error handling](error-handling.md)

## Typical workflow

1. Define a mapper by implementing `MapperDefinitionInterface`.
2. Configure the fields and any required transformations or constraints.
3. Configure the target model through the mapper's options.
4. Inject `MapperInterface` into your application service.
5. Call `build()` with the mapper class and input data.
6. Handle mapping and validation failures at the appropriate application boundary.

The examples throughout these guides use illustrative classes such as `User`, `Address`, and their corresponding mapper definitions. Adapt the namespaces and model fields to your application.
