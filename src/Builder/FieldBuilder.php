<?php

namespace Netmex\HydratorBundle\Builder;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;

class FieldBuilder implements BuilderInterface
{
    private array $fields = [];

    public function add(string $field, TransformerInterface|string|MapperDefinitionInterface $type = null, ?array $constraints = [], array $options = []): static
    {
        $key = match (true) {
            $type instanceof MapperDefinitionInterface => 'MapperDefinition',
            default => 'Transformer',
        };

        $this->fields[$field] = [
            $key => $type,
            'constraints' => $constraints,
            'options' => $options
        ];

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
