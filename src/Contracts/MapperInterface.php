<?php

namespace Netmex\HydratorBundle\Contracts;

interface MapperInterface
{
    public function build(string $mapperClass, array $inputData, ?object $target = null, array $options = []): object|array;
}
