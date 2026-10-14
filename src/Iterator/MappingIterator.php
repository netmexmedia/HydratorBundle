<?php

namespace Netmex\HydratorBundle\Iterator;

use Generator;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Exception\InvalidMappingInputException;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Hydrator\ObjectHydrator;

final readonly class MappingIterator
{
    public function __construct(
        private MapperFactory $mapperFactory,
        private ObjectHydrator $objectHydrator,
    ) {}

    /**
     * @return Generator<int, object>
     *
     * @throws InvalidMappingInputException
     * @throws MappingValidationException
     */
    public function iterate(MapperDefinitionInterface $mapper, array $inputData, array $options = []): Generator
    {
        if (!array_is_list($inputData)) {
            throw InvalidMappingInputException::expectedList();
        }

        foreach ($inputData as $data) {
            if (!is_array($data)) {
                throw InvalidMappingInputException::expectedArrayItem();
            }

            $definition = $this->mapperFactory->create(
                $mapper,
                $data,
                options: $options,
            );

            yield $this->objectHydrator->hydrate($definition);
        }
    }
}