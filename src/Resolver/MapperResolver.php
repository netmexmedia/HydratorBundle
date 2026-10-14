<?php

namespace Netmex\HydratorBundle\Resolver;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Contracts\OptionsResolverInterface;

final readonly class MapperResolver
{
    public function resolve(string $mapper, BuilderInterface $builder, OptionsResolverInterface $optionsResolver): MapperDefinitionInterface
    {
        if (!class_exists($mapper)) {
            throw new \InvalidArgumentException("Mapper class '{$mapper}' does not exist.");
        }

        $instance = new $mapper();

        if (!$instance instanceof MapperDefinitionInterface) {
            throw new \RuntimeException("Class '{$mapper}' must implement MapperDefinitionInterface.");
        }

        $instance->process($builder);
        $instance->options($optionsResolver);

        return $instance;
    }
}