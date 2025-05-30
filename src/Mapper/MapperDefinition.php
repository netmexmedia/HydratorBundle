<?php

namespace Netmex\HydratorBundle\Mapper;

class MapperDefinition
{
    private string $model;

    private FieldCollection $fields;

    public function __construct(string $model, FieldCollection $fields) {
        $this->model = $model;
        $this->fields = $fields;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getFields(): FieldCollection
    {
        return $this->fields;
    }
}