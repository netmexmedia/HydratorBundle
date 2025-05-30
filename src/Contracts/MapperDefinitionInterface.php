<?php

namespace Netmex\HydratorBundle\Contracts;

use Netmex\HydratorBundle\Options\OptionsResolver;

interface MapperDefinitionInterface
{
    public function process(BuilderInterface $builder): void;

    public function options(OptionsResolver $resolver): void;
}