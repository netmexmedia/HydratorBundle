# Validation

Netmex HydratorBundle integrates with Symfony Validator to validate mapped fields using Symfony constraints.

## Define constraints

Constraints are supplied when defining a field:

```php
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

$builder->add('name', null, [
    NotBlank::class => null,
    Length::class => [
        'min' => 2,
        'max' => 50,
    ],
]);
```

In this example:

- `NotBlank` rejects a blank value.
- `Length` restricts the value to the specified length range.

The bundle supports Symfony constraints through its field-validation integration. Consult the Symfony documentation for the complete list of available constraints and their options.

See [Symfony Validation Constraints](https://symfony.com/doc/current/validation.html#constraints).

## Validate transformed values

Transformers and constraints can be defined on the same field:

```php
$builder->add('name', CapitalizationTransformer::class, [
    NotBlank::class => null,
]);
```

Check your intended validation behavior when transformations can change a value. The validation order is determined by the bundle's field-processing implementation.

## Handle validation failures

A mapping may fail validation. Catch the bundle's mapping validation exception at the application boundary and convert the error into a format suitable for your application.

```php
try {
    $user = $mapper->build(
        UserHydrator::class,
        $inputData,
    );
} catch (MappingValidationException $exception) {
    // Convert the failure into an application-specific response.
}
```

Import the exception used by your bundle version:

```php
use Netmex\HydratorBundle\Exception\MappingValidationException;
```

The exact information available from the exception depends on the bundle's exception API. If it exposes Symfony violations, you can use those to produce structured field-level errors.

## Validation is not input parsing

Validation constraints do not replace input parsing. For example, a controller receiving JSON should first handle malformed JSON and unexpected top-level data types before passing the decoded input to the mapper.

Keep parsing errors, mapping input errors, and validation failures distinct in your API responses.
