<?php

namespace Netmex\HydratorBundle\Tests\Mapper;

use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;

class DummyMapperClass implements MapperDefinitionInterface
{
    public function process($builder): void {}
    public function options($resolver): void {}
}
