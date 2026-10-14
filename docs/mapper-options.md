# Mapper Options

Mapper options configure how a mapping is executed.

Options are declared in the mapper definition's `options()` method using the bundle's `OptionsResolver`.

## Set default options

```php
public function options(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'model' => User::class,
    ]);
}
```

The `model` option identifies the target class used for hydration.

## Override options at runtime

The mapper's `build()` method accepts an options array:

```php
$result = $mapper->build(
    UserHydrator::class,
    $inputData,
    options: $options,
);
```

Runtime options can be used when you need to alter supported mapping behavior for a particular call.

Only pass options that the mapper and bundle recognize.

## Multiple-object hydration

The bundle supports collection hydration. The mapper options determine whether the supplied input is processed as a single object or multiple objects.

```php
$result = $mapper->build(
    UserHydrator::class,
    $listOfUsers,
    options: $options,
);
```

Configure the multiple-object behavior using the exact option supported by your bundle version.

When multiple-object hydration is enabled, the input is expected to be a list of arrays, and the result is an array of hydrated objects.

See [Collection Hydration](collections.md) for details.

## Choosing options

Prefer declaring stable defaults in the mapper definition. Use runtime options when a particular mapping operation needs different behavior.

For the complete list of accepted options and their defaults, consult the bundle's option-resolution implementation.
