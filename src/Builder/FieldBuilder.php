<?php

namespace Netmex\HydratorBundle\Builder;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;

class FieldBuilder implements BuilderInterface
{
    private array $fields;

    public function add(string $field, TransformerInterface|string $type, ?array $constraints): static
    {
        $this->fields[$field] = [
            'Transformer' => $type,
            'constraints' => $constraints
        ];

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}