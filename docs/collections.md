# Collection Hydration

Netmex HydratorBundle supports hydrating multiple objects from a list of input arrays.

This is useful when processing records received from an API, importing structured data, or creating several DTOs from one request.

## Input structure

A collection should be a list of arrays, with each array containing the input data for one object:

```php
$data = [
    [
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ],
    [
        'name' => 'Bob',
        'email' => 'bob@example.com',
    ],
];
```

Each item is mapped using the same mapper definition.

## Hydrate the collection

Pass the collection to `build()` and enable multiple-object hydration using the supported mapper option:

```php
$users = $mapper->build(
    UserHydrator::class,
    $data,
    options: $options,
);
```

Configure `$options` with the multiple-object setting defined by your bundle.

The result is an array of hydrated objects.

## Input requirements

The collection must be a list, rather than an associative array representing a single object.

Each item must itself be an array.

Invalid collection structures can result in an input exception.

## Validation

Each item is processed using the mapper definition. Constraints and transformers defined for its fields are applied according to the bundle's mapping behavior.

Handle mapping and validation failures at the application boundary. Decide whether your application should reject the entire collection when one item fails or report failures according to your API's requirements.
