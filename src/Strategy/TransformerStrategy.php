<?php

namespace Netmex\HydratorBundle\Strategy;

use Netmex\HydratorBundle\Contracts\FieldHydrationStrategyInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;
use Netmex\HydratorBundle\Field\FieldDefinition;

class TransformerStrategy implements FieldHydrationStrategyInterface
{
    public function supports(FieldDefinition $field): bool
    {
        return $field->getType() instanceof TransformerInterface;
    }

    public function hydrate(FieldDefinition $field): mixed
    {
        $field->transform();

        return $field->getValue();
    }
}