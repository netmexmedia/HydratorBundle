<?php

namespace Netmex\HydratorBundle\Mapper;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperInterface;
use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Processor\MappingProcessor;
use Netmex\HydratorBundle\Resolver\MapperResolver;
use Netmex\HydratorBundle\Resolver\OptionsResolver;

final readonly class Mapper implements MapperInterface
{
    public function __construct(
        private BuilderInterface $builder,
        private OptionsResolver $optionsResolver,
        private MapperResolver $mapperResolver,
        private ValidatorInterface $validator,
        private MappingProcessor $mappingProcessor,
    ) {}

    /**
     * @throws MappingValidationException
     */
    public function build(string $mapperClass, array $inputData, ?object $target = null, array $options = []): object|array
    {
        $this->validator->resetErrors();

        $mapper = $this->mapperResolver->resolve(
            $mapperClass,
            $this->builder,
            $this->optionsResolver,
        );

        return $this->mappingProcessor->map(
            $mapper,
            $inputData,
            $target,
            $options,
        );
    }
}
