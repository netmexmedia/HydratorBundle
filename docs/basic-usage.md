# Basic Usage

This guide explains how to define a mapper and use it to hydrate an object.

## Define a mapper

A mapper definition implements `MapperDefinitionInterface` and describes the fields to process and the options used for hydration.

```php
<?php

namespace App\Hydrator;

use App\Entity\User;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Resolver\OptionsResolver;

final class UserHydrator implements MapperDefinitionInterface
{
    public function process(BuilderInterface $builder): void
    {
        $builder
            ->add('name')
            ->add('email');
    }

    public function options(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'model' => User::class,
        ]);
    }
}
```

### The `process()` method

The `process()` method defines the mapping rules through `BuilderInterface`.

Each call to `add()` declares a field to map. Depending on the mapping, a field can also use a transformer, validation constraints, or a nested mapper definition.

For example:

```php
$builder->add('name');
```

This declares a mapping for the `name` field.

A transformed and validated field can be defined as follows:

```php
$builder->add('name', CapitalizationTransformer::class, [
    NotBlank::class => null,
    Length::class => [
        'min' => 2,
        'max' => 50,
    ],
]);
```

The available arguments and their supported combinations are defined by the bundle's `BuilderInterface` and field-building implementation.

### The `options()` method

The `options()` method configures how the mapping is executed.

A common option is the target model:

```php
public function options(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'model' => User::class,
    ]);
}
```

The target model determines which class is hydrated when the mapper builds an object.

See [Mapper Options](mapper-options.md) for further details.

## Hydrate input data

Inject `MapperInterface` and pass the mapper definition class and input data to `build()`:

```php
$user = $mapper->build(UserHydrator::class, [
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ],
);
```

The result is the hydrated target object.

## Use the mapper in a controller

```php
<?php

namespace App\Controller;

use App\Hydrator\UserHydrator;
use Netmex\HydratorBundle\Contracts\MapperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class UserController
{
    #[Route('/users', methods: ['POST'])]
    public function create(Request $request, MapperInterface $mapper): JsonResponse 
    {
        $data = json_decode(
            $request->getContent(),
            true,
        );

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return new JsonResponse(
                ['error' => 'Invalid input data.'],
                400,
            );
        }

        $user = $mapper->build(UserHydrator::class, $data);

        return new JsonResponse([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
}
```

This is a simplified example. In a real application, handle mapping and validation exceptions and serialize your response according to your API's requirements.

## Reuse mapper definitions

Mapper definitions allow mapping rules to live in dedicated classes rather than being repeated across controllers or services.

A single definition can be reused wherever the same input structure and target model are required.
