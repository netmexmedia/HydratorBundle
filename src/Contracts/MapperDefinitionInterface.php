<?php

namespace Netmex\HydratorBundle\Contracts;

interface MapperDefinitionInterface
{
    public function process(BuilderInterface $builder): void;

    public function options(OptionsResolverInterface $resolver): void;
}