<?php

namespace Netmex\HydratorBundle\Hydrator;

use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Field\FieldDefinition;
use Netmex\HydratorBundle\Mapping\NestedMapping;

final readonly class FieldHydrator
{
    public function __construct(
        private iterable           $strategies,
        private ValidatorInterface $validator,
    ) {
    }

    public function hydrate(FieldDefinition $field): mixed
    {
        $this->validator->validate($field, 'input');

        if ($field->getValue() === null) {
            return null;
        }

        foreach ($this->strategies as $strategy) {
            if (!$strategy->supports($field)) {
                continue;
            }

            $result = $strategy->hydrate($field);

            if ($result instanceof NestedMapping) {
                return $result;
            }

            $field->setValue($result);

            break;
        }

        $this->validator->validate($field, 'output');

        return $field->getValue();
    }

    public function validateOutput(FieldDefinition $field): void
    {
        $this->validator->validate($field, 'output');
    }
}
