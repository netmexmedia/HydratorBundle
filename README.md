# Netmex HydratorBundle

## About
The Netmex HydratorBundle is a Symfony bundle designed to simplify the process of hydrating data transfer objects (DTOs) from arrays or JSON inputs with built-in support for data transformation and validation using Symfony constraints.

It enables you to:
* Define mappers that transform and validate input data.
* Easily create reusable transformers to convert raw data (e.g., capitalization, formatting).
* Handle validation errors gracefully.
* Integrate seamlessly into Symfony controllers for clean and maintainable code.

## Installation

```bash
composer require netmex/hydrator-bundle
```

## Usage

Create a mapper class that implements
```Netmex\HydratorBundle\Contracts\MapperDefinitionInterface```.

##### Example Hydrator
```php
<?php

namespace App\Hydrator;

use App\Entity\MyClass;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Transformer\CapitalizationTransformer;

class Hydrator implements MapperDefinitionInterface
{
    public function process(BuilderInterface $builder): void
    {
        $builder
            ->add('key', CapitalizationTransformer::class, [
                NotBlank::class => null,
                Length::class => ['min' => 6, 'max' => 7],
            ]);
    }

    public function options(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'model' => MyClass::class,
        ]);
    }
}
```

### Create a Transformer

```php
<?php

namespace App\Transformer;

use Netmex\HydratorBundle\Contracts\TransformerInterface;

class CapitalizationTransformer implements TransformerInterface
{
    public function transform(string $data): string
    {
        return strtoupper($data);
    }
}
```

### Injecting the Mapper into a Controller

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Hydrator\Hydrator;
use Netmex\HydratorBundle\Contracts\MapperInterface;
use Netmex\HydratorBundle\Exception\ValidationFailedException;

class ExampleController
{
    #[Route('/example', name: 'app_example')]
    public function index(Request $request, MapperInterface $hydrator): Response
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        try {
            $result = $hydrator->build(Hydrator::class, $data);
        } catch (ValidationFailedException $e) {
            return new JsonResponse(['errors' => (string) $e->getViolations()], 400);
        }

        return new JsonResponse($result);
    }
}
```

## Recommended Directory Layout
```text
src/
├── Hydrator/
│   └── Hydrator.php
├── Transformer/
│   └── CapitalizationTransformer.php
└── Controller/
    └── ExampleController.php
```

## Exception Handling
```php
try {
    $data = $hydrator->build(Hydrator::class, $requestData);
} catch (ValidationFailedException $e) {
    return new JsonResponse(['errors' => (string) $e->getViolations()], 400);
}
```

## More about Constraints
See [Symfony Validation Constraints](https://symfony.com/doc/current/validation.html#constraints) for available options.
