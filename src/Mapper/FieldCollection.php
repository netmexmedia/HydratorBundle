<?php

namespace Netmex\HydratorBundle\Mapper;

class FieldCollection implements \IteratorAggregate, \Countable
{
    /** @var FieldDefinition[] */
    private array $fields = [];

    public function __construct(array $fields)
    {
        foreach ($fields as $field) {
            $this->add($field);
        }
    }

    public function add(FieldDefinition $field): void
    {
        $this->fields[$field->name] = $field;
    }

    public function get(string $name): ?FieldDefinition
    {
        return $this->fields[$name] ?? null;
    }

    public function all(): array
    {
        return $this->fields;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->fields);
    }

    public function count(): int
    {
        return count($this->fields);
    }

    public function requiredOnly(): array
    {
        return array_filter($this->fields, fn(FieldDefinition $f) => $f->options['required'] ?? false);
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}