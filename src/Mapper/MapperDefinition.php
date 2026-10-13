<?php

namespace Netmex\HydratorBundle\Mapper;

use Netmex\HydratorBundle\Options\MapperOptions;

class MapperDefinition
{
    private FieldCollection $fields;

    private ?object $target;

    public function __construct(FieldCollection $fields, ?object $target = null, public MapperOptions $options) {
        $this->fields = $fields;
        $this->target = $target;

    }

    public function getFields(): FieldCollection
    {
        return $this->fields;
    }

    public function getTarget(): ?object
    {
        return $this->target;
    }

    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function getOptions(): MapperOptions
    {
        return $this->options;
    }
}
