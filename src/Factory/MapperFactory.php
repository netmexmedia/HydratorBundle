<?php

namespace Netmex\HydratorBundle\Factory;
use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Mapper\FieldCollection;
use Netmex\HydratorBundle\Mapper\MapperDefinition;
use Netmex\HydratorBundle\Options\MapperOptions;
use Netmex\HydratorBundle\Resolver\OptionsResolver;

final readonly class MapperFactory
{
    private BuilderInterface $builder;
    private OptionsResolver $resolver;

    public function __construct(BuilderInterface $builder, OptionsResolver $resolver, private FieldFactory $fieldFactory)
    {
        $this->builder = $builder;
        $this->resolver = $resolver;
    }

    public function create(MapperDefinitionInterface $mapper, array $data, ?object $target = null, array $options = []): MapperDefinition
    {
        $mapper->process($this->builder);
        $mapper->options($this->resolver);


        $options = $this->resolver->resolve(
            array_replace(
                $this->resolver->getDefaults(),
                $options,
            ),
            MapperOptions::class
        );


        $fields = [];

        foreach ($this->builder->getFields() as $name => $fieldInfo) {
            $fields[] = $this->fieldFactory->create(
                $name,
                $fieldInfo,
                $data,
            );
        }

        return new MapperDefinition(
            fields: new FieldCollection($fields),
            target: $target,
            options: $options,
        );
    }
}
