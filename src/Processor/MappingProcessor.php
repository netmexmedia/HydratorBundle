<?php

namespace Netmex\HydratorBundle\Processor;

use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Hydrator\ObjectHydrator;
use Netmex\HydratorBundle\Iterator\MappingIterator;

final readonly class MappingProcessor
{
    public function __construct(
        private MapperFactory $mapperFactory,
        private ObjectHydrator $objectHydrator,
        private MappingIterator $mappingIterator,
    ) {}

    /**
     * @throws MappingValidationException
     */
    public function map(MapperDefinitionInterface $mapper, array $inputData, ?object $target = null, array $options = []): object|array
    {
        $definition = $this->mapperFactory->create(
            $mapper,
            $inputData,
            $target,
            $options,
        );

        if (!$definition->getOptions()->isMultiple()) {
            return $this->objectHydrator->hydrate($definition);
        }

        return iterator_to_array(
            $this->mappingIterator->iterate(
                $mapper,
                $inputData,
                $options,
            ),
            false,
        );
    }
}