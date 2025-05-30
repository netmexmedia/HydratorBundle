<?php

namespace Netmex\HydratorBundle\Factory;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Mapper\FieldCollection;
use Netmex\HydratorBundle\Mapper\FieldDefinition;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use Netmex\HydratorBundle\Options\OptionsResolver;
use Netmex\HydratorBundle\Registry\TransformerLocator;

class MapperFactory
{
    private BuilderInterface $builder;
    private OptionsResolver $resolver;
    private TransformerLocator $transformerLocator;

    public function __construct(BuilderInterface $builder, OptionsResolver $resolver, TransformerLocator $transformerLocator)
    {
        $this->builder = $builder;
        $this->resolver = $resolver;
        $this->transformerLocator = $transformerLocator;
    }

    public function create(object $mapper, array $data): MapperDefinition
    {
        $mapper->process($this->builder);
        $mapper->options($this->resolver);

        $resolved = $this->resolver->resolve($this->builder->getFields());

        $fields = [];

        foreach ($resolved['fields'] as $name => $fieldInfo) {
            $transformer = $this->transformerLocator->get($fieldInfo['Transformer']);
            $fields[] = new FieldDefinition(
                name: $name,
                transformer: $transformer,
                value: $data[$name] ?? null,
                constraints: $fieldInfo['constraints'] ?? [],
            );
        }

        return new MapperDefinition(
            model: $resolved['model'],
            fields: new FieldCollection($fields)
        );
    }
}