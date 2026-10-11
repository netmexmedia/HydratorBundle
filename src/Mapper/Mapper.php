<?php

namespace Netmex\HydratorBundle\Mapper;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Contracts\MapperInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;
use Netmex\HydratorBundle\Contracts\ValidatorInterface;
use Netmex\HydratorBundle\Exception\MappingValidationException;
use Netmex\HydratorBundle\Factory\MapperFactory;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use \Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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

    public function build(string $mapperClass, array $inputData, ?object $target = null): object
    {
        $this->validator->resetErrors();

        $mapperDefinition = $this->mapperResolver->resolve($mapperClass, $this->builder, $this->optionsResolver);
        $definition = $this->hydrationDefinitionFactory->create($mapperDefinition, $inputData, $target);
        $transformedData = $this->processFields($definition);

        if ($this->validator->hasErrors()) {
            throw new MappingValidationException($this->validator->getErrors());
        }

        return $this->serializer->denormalize(
            $transformedData,
            $definition->getModel(),
            null,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $definition->getTarget()
            ]
        );
    }

    private function processFields(MapperDefinition $definition): array
    {
        $transformedData = [];

        /** @var FieldDefinition $field */
        foreach ($definition->getFields() as $field) {
            $this->validator->validate($field, 'input');

            if ($field->getValue() === null) {
                continue;
            }

            $type = $field->getType();

            if ($type instanceof TransformerInterface) {
                $field->transform();
            } elseif ($type instanceof MapperDefinitionInterface) {
                $nestedDefinition = $this->hydrationDefinitionFactory->create(
                    $type,
                    $field->getValue()
                );

                $nestedData = $this->processFields($nestedDefinition);

                $nestedObject = $this->serializer->denormalize(
                    $nestedData,
                    $nestedDefinition->getModel(),
                    null,
                    [
                        AbstractNormalizer::OBJECT_TO_POPULATE =>
                            $nestedDefinition->getTarget(),
                    ]
                );

                $field->setValue($nestedObject);
            }

            $this->validator->validate($field, 'output');

            $transformedData[$field->getName()] = $field->getValue();
        }

        return $transformedData;
    }
}
