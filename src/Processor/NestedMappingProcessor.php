<?php

namespace Netmex\HydratorBundle\Processor;

use Netmex\HydratorBundle\Exception\InvalidNestedMappingInputException;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Hydrator\ObjectHydrator;
use Netmex\HydratorBundle\Mapping\NestedMapping;

final readonly class NestedMappingProcessor
{
    public function __construct(
        private MapperFactory $mapperFactory,
        private ObjectHydrator $objectHydrator,
    ) {}

    /**
     * @throws MappingValidationException
     */
    public function process(NestedMapping $mapping): object|array
    {
        $items = $mapping->multiple
            ? $mapping->value
            : [$mapping->value];

        if (!is_array($items)) {
            throw $mapping->multiple ? InvalidNestedMappingInputException::expectedList($mapping->fieldName,) : InvalidNestedMappingInputException::expectedObjectData($mapping->fieldName);
        }

        if ($mapping->multiple && !array_is_list($items)) {
            throw InvalidNestedMappingInputException::expectedList(
                $mapping->fieldName,
            );
        }

        $objects = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                throw InvalidNestedMappingInputException::expectedArrayItem(
                    $mapping->fieldName,
                );
            }

            $definition = $this->mapperFactory->create(
                $mapping->type,
                $item,
            );

            $objects[] = $this->objectHydrator->hydrate($definition);
        }

        return $mapping->multiple ? $objects : $objects[0];
    }
}