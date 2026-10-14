<?php

namespace Netmex\HydratorBundle\Contracts;

use Netmex\HydratorBundle\Field\FieldDefinition;

interface FieldHydrationStrategyInterface
{
    public function supports(FieldDefinition $field): bool;

    public function hydrate(FieldDefinition $field): mixed;
}