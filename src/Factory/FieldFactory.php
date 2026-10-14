<?php

namespace Netmex\HydratorBundle\Factory;

use Netmex\HydratorBundle\Field\FieldDefinition;
use Netmex\HydratorBundle\Options\FieldOptions;
use Netmex\HydratorBundle\Resolver\OptionsResolver;

final readonly class FieldFactory
{
    public function __construct(
        private OptionsResolver $fieldOptionsResolver,
    ) {

    }

    public function create(string $name, array $fieldInfo, array $data): FieldDefinition
    {
        $type = match (true) {
            isset($fieldInfo['Transformer']) => new $fieldInfo['Transformer'](),
            isset($fieldInfo['MapperDefinition']) => $fieldInfo['MapperDefinition'],

            default => null,
        };

        $options = $this->fieldOptionsResolver->resolve(
            $fieldInfo['options'] ?? [],
            FieldOptions::class
        );

        return new FieldDefinition(
            name: $name,
            type: $type,
            value: $data[$name] ?? null,
            constraints: $fieldInfo['constraints'] ?? [],
            options: $options,
        );
    }
}
