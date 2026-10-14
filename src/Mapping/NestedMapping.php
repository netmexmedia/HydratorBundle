<?php

namespace Netmex\HydratorBundle\Mapping;

use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;

final readonly class NestedMapping
{
    public function __construct(
        public MapperDefinitionInterface $type,
        public mixed                     $value,
        public bool                      $multiple,
        public string                    $fieldName,
    ) {
    }
}