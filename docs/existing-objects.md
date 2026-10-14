# Hydrating Existing Objects

In addition to hydrating a new target object, the bundle's `build()` method accepts an optional target object.

This can be useful when updating an existing object from incoming data.

## Example

```php
$existingUser = $repository->find($id);

if ($existingUser === null) {
    throw $this->createNotFoundException();
}

$result = $mapper->build(
    UserHydrator::class,
    $inputData,
    $existingUser,
);
```

The supplied target should be compatible with the model expected by the mapper definition.

## When to use an existing target

Hydrating an existing object may be useful when:

- Updating a previously loaded entity.
- Applying incoming data to an existing DTO or model.
- Reusing an object whose identity must be preserved.

## Important considerations

Hydration is not automatically equivalent to a complete update strategy.

Your application should define how missing fields, invalid values, nested objects, and validation failures affect the existing object. Test these behaviors before using hydration in operations that modify persistent entities.
