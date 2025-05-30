<?php

namespace Netmex\HydratorBundle\Contracts;

use Netmex\HydratorBundle\Mapper\FieldDefinition;

interface MapperInterface
{
    public function build(string $mapperClass, array $inputData): object;
}