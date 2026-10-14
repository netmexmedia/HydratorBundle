# Nested and Recursive Mapping

Netmex HydratorBundle supports mapping fields into nested objects using other mapper definitions.

Nested mapping allows complex object structures to be built from structured input data without putting all mapping rules into one class.

## Nested objects

Suppose a `User` contains an `Address` object.

The user input might look like this:

```php
$data = [
    'name' => 'Alice',
    'address' => [
        'street' => 'Main Street',
        'city' => 'Amsterdam',
    ],
];
```

Define an address mapper:

```php
<?php

namespace App\Hydrator;

use App\Entity\Address;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Resolver\OptionsResolver;

final class AddressHydrator implements MapperDefinitionInterface
{
    public function process(BuilderInterface $builder): void
    {
        $builder
            ->add('street')
            ->add('city');
    }

    public function options(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'model' => Address::class,
        ]);
    }
}
```

Use the nested mapper in the parent definition:

```php
$builder
    ->add('name')
    ->add('address', new AddressHydrator());
```

The `AddressHydrator` defines how the nested address data is mapped.

Nested mapper definitions can contain their own field mappings, transformers, and validation constraints.

## Recursive structures

Some data models contain repeating relationships. For example, a category may contain child categories, which can contain further child categories.

A conceptual input structure could look like this:

```php
$data = [
    'name' => 'Electronics',
    'children' => [
        [
            'name' => 'Computers',
            'children' => [
                [
                    'name' => 'Laptops',
                    'children' => [],
                ],
            ],
        ],
    ],
];
```

This kind of input has multiple levels of nesting.

Recursive hydration means that the mapping process can handle the repeated nested structure rather than requiring a separate mapper class for each depth.

The exact recursive mapping declaration depends on the supported mapper-definition API and how recursive references are configured. Use the recursive pattern supported by your bundle implementation; the conceptual data structure above is not, by itself, a complete working mapper definition.

## Input expectations

Nested fields must provide data in the structure expected by their nested mapper.

Depending on whether the field represents one object or multiple objects, that may mean:

- An array containing one object's field data.
- A list of arrays containing field data for multiple objects.

Malformed nested input can result in a mapping input exception.

## Design recommendations

- Keep mapper definitions focused on a specific object or reusable mapping.
- Use nested mapping to compose larger object structures.
- Validate the fields of nested objects through their respective mapper definitions.
- Test recursive structures at several depths, including empty child collections and malformed nested values.
- Be mindful of deeply nested or cyclic input data when accepting untrusted input.

Recursive object relationships and recursive input arrays are not necessarily the same thing. If your domain model contains cyclic object references, document and test how your application handles them separately.
