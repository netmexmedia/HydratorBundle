<?php

namespace Netmex\HydratorBundle\Mapper;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperInterface;
use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Mapper implements MapperInterface
{
    private BuilderInterface $builder;
    private OptionsResolver $optionsResolver;
    private MapperFactory $hydrationDefinitionFactory;
    private MapperResolver $mapperResolver;
    private ValidatorInterface $validator;
    private array $violations = [];
    private DenormalizerInterface $serializer;

    public function __construct(
        BuilderInterface $builder,
        OptionsResolver $optionsResolver,
        MapperFactory $hydrationDefinitionFactory,
        MapperResolver $mapperResolver,
        ValidatorInterface $validator,
        DenormalizerInterface $serializer
    )
    {
        $this->builder = $builder;
        $this->optionsResolver = $optionsResolver;
        $this->hydrationDefinitionFactory = $hydrationDefinitionFactory;
        $this->mapperResolver = $mapperResolver;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function build(string $mapperClass, array $inputData): object
    {
        $this->validator->resetErrors();
        $transformedData = [];

        $transformer = $this->mapperResolver->resolve($mapperClass, $this->builder, $this->optionsResolver);
        $definition = $this->hydrationDefinitionFactory->create($transformer, $inputData);
        $fields = $definition->getFields();

        foreach ($fields as $field) {
            $this->validator->validate($field, 'input');

            if ($field->getValue() === null) {
                continue;
            }

            $field->transform();

            $this->validator->validate($field, 'output');

            $transformedData[$field->getName()] = $field->getValue();
        }

        if ($this->validator->hasErrors()) {
            throw new MappingValidationException($this->validator->getErrors());
        }

        return $this->serializer->denormalize($transformedData, $definition->getModel());
    }

}
