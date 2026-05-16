<?php

namespace Netmex\HydratorBundle\Mapper;

class MapperDefinition
{
    private string $model;

    private FieldCollection $fields;

    private ?object $target;

    public function __construct(string $model, FieldCollection $fields, ?object $target = null) {
        $this->model = $model;
        $this->fields = $fields;
        $this->target = $target;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getFields(): FieldCollection
    {
        return $this->fields;
    }

    public function getTarget(): ?object
    {
        return $this->target;
    }
}
