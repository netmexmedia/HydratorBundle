<?php

namespace Netmex\HydratorBundle\Iterator;

use Generator;
use IteratorAggregate;
use Netmex\HydratorBundle\Hydrator\FieldHydrator;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use Netmex\HydratorBundle\Mapping\NestedMapping;
use Netmex\HydratorBundle\Processor\NestedMappingProcessor;

/**
 * @implements IteratorAggregate<string, mixed>
 */
readonly class FieldIterator implements IteratorAggregate
{
    public function __construct(
        private FieldHydrator $fieldHydrator,
        private NestedMappingProcessor $nestedMappingProcessor,
    ) {}

    public function iterate(MapperDefinition $definition): Generator
    {
        foreach ($definition->getFields() as $field) {

            $result = $this->fieldHydrator->hydrate($field);

            if ($result instanceof NestedMapping) {
                $field->setValue(
                    $this->nestedMappingProcessor->process($result),
                );

                $this->fieldHydrator->validateOutput($field);
            }

            if ($field->getValue() !== null) {
                yield $field->getName() => $field->getValue();
            }
        }
    }

    public function getIterator(): Generator
    {
        throw new \LogicException(
            'Use iterate() with a MapperDefinition.',
        );
    }
}