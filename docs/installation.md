# Installation and Configuration

## Requirements

Netmex HydratorBundle requires PHP and Symfony components compatible with the versions declared in the package's `composer.json`.

The bundle integrates with Symfony's dependency injection container and uses Symfony Validator for field validation.

## Install with Composer

```bash
composer require netmex/hydrator-bundle
```

If your application does not automatically register Symfony bundles, register the bundle according to your application's Symfony configuration.

## Service integration

The bundle registers the services needed to resolve mapper definitions, build mappings, hydrate objects, process nested mappings, and validate field values.

In a standard Symfony application, inject the public mapper contract into your service:

```php
use Netmex\HydratorBundle\Contracts\MapperInterface;

final class UserService
{
    public function __construct(
        private readonly MapperInterface $mapper,
    ) {}

    public function createUser(array $data): object|array
    {
        return $this->mapper->build(
            UserHydrator::class,
            $data,
        );
    }
}
```

The return type shown here reflects the mapper's general return type. For a particular mapper definition, the actual result depends on its configuration.

## Transformer configuration

The bundle supports custom transformers registered as Symfony services.

The bundle's configuration includes settings for the transformer directory, namespace, and service tag. Refer to your bundle's configuration definition and example configuration for the exact option names and defaults.

Ensure that transformer services receive the tag expected by the bundle so they can be discovered by its transformer locator.

## Configuration example

The bundle may be configured in your application's package configuration. Use the actual root configuration key and supported option names from your installed bundle version.

```yaml
# config/packages/netmex_hydrator.yaml
netmex_hydrator:
    # Configure transformer discovery here.
```

The snippet above is a placeholder rather than a complete working configuration. Consult your bundle's `Configuration` class for the accepted keys and default values.
