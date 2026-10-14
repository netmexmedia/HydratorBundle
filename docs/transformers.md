# Transformers

Transformers let you convert input values before they are assigned to the target object.

They are useful for normalizing strings, formatting values, or applying reusable conversion logic.

## Create a transformer

Implement `TransformerInterface`:

```php
<?php

namespace App\Transformer;

use Netmex\HydratorBundle\Contracts\TransformerInterface;

final class CapitalizationTransformer implements TransformerInterface
{
    public function transform(string $data): string
    {
        return strtoupper($data);
    }
}
```

This transformer converts a string to uppercase.

## Use a transformer in a mapper

Pass the transformer class when defining a field:

```php
$builder->add(
    'name',
    CapitalizationTransformer::class,
);
```

The bundle uses its field-processing strategy to apply the configured transformer.

## Combine transformations and validation

A field can declare both a transformer and validation constraints:

```php
$builder->add('name', CapitalizationTransformer::class, [
    NotBlank::class => null,
    Length::class => [
        'min' => 2,
        'max' => 50,
    ],
]);
```

Import the relevant constraints:

```php
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
```

Validation rules should reflect the requirements of the target field. Verify the bundle's transformation and validation order if your constraints depend on whether they receive the original or transformed value.

## Register transformers

Transformers must be registered so the bundle can discover and resolve them.

The bundle uses a transformer service tag and a transformer locator. Use the tag configured by your bundle, or configure the transformer tag through the bundle's supported configuration.

Example service definition:

```yaml
services:
    App\Transformer\CapitalizationTransformer:
        tags:
            - 'netmex.transformer'
```

If your application uses automatic service registration, ensure the transformer is included in the relevant service resource and that the required tag is applied.

## Reuse transformers

A transformer can be used by multiple mapper definitions. This keeps common conversion logic out of your controllers and mapper definitions.
