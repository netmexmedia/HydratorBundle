<?php

namespace Netmex\HydratorBundle\Mapper;

use Netmex\HydratorBundle\Contracts\BuilderInterface;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Options\OptionsResolver;

class MapperResolver
{
    public function resolve(string $mapper, BuilderInterface $builder, OptionsResolver $optionsResolver)
    {
        if (!class_exists($mapper)) {
            throw new \InvalidArgumentException("Transformer class '{$mapper}' does not exist.");
        }

        $instance = new $mapper();

        if (!$instance instanceof MapperDefinitionInterface) {
            throw new \RuntimeException("Class '{$mapper}' must implement TransformerInterface.");
        }

        $instance->process($builder);
        $instance->options($optionsResolver);

        return $instance;
    }
}