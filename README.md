# Netmex HydratorBundle

Netmex HydratorBundle is a Symfony bundle for mapping, transforming, and validating input data into PHP objects and data transfer objects (DTOs).

Define reusable mapper definitions to control how input fields are processed, how values are transformed, and how validation constraints are applied. The bundle also supports nested mappings, recursive hydration, and mapping collections of input data.

## Features

- Reusable mapper definitions
- Custom field transformers
- Symfony Validator constraints
- Nested and recursive hydration
- Collection hydration
- Hydration of existing objects
- Configurable mapping options
- Integration with Symfony's service container

## Installation

```bash
composer require netmex/hydrator-bundle
```

See the [installation guide](docs/installation.md) for configuration details.

## Quick start

Create a mapper definition by implementing `MapperDefinitionInterface`:

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

Inject `MapperInterface` into your service or controller and use `build()`:

```php
$user = $mapper->build(UserHydrator::class, $inputData);
```

The input data should be an associative array containing the fields expected by the mapper.

## Documentation

- [Installation and configuration](docs/installation.md)
- [Basic usage](docs/basic-usage.md)
- [Mapper options](docs/mapper-options.md)
- [Transformers](docs/transformers.md)
- [Validation](docs/validation.md)
- [Nested and recursive mapping](docs/nested-and-recursive-mapping.md)
- [Collection hydration](docs/collections.md)
- [Existing objects](docs/existing-objects.md)
- [Error handling](docs/error-handling.md)

## Requirements

Check `composer.json` for the supported PHP and Symfony versions.

## License

See the repository's `LICENSE` file.
