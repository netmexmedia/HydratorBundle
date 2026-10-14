<?php

namespace Netmex\HydratorBundle\Strategy;

use Netmex\HydratorBundle\Contracts\FieldHydrationStrategyInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Field\FieldDefinition;
use Netmex\HydratorBundle\Mapping\NestedMapping;

class MapperStrategy implements FieldHydrationStrategyInterface
{
    public function supports(FieldDefinition $field): bool
    {
        return $field->getType() instanceof MapperDefinitionInterface;
    }

    public function hydrate(FieldDefinition $field): NestedMapping
    {
        return new NestedMapping(
            $field->getType(),
            $field->getValue(),
            $field->getOptions()->isMultiple(),
            $field->getName(),
        );
    }
}