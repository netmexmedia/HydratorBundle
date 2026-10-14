<?php

namespace Netmex\HydratorBundle\Mapper;

use Netmex\HydratorBundle\Field\FieldCollection;
use Netmex\HydratorBundle\Options\MapperOptions;

final readonly class MapperDefinition
{
    public function __construct(
        private FieldCollection $fields,
        private ?object         $target = null,
        public MapperOptions    $options
    ) {}

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
