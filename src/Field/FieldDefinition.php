<?php

namespace Netmex\HydratorBundle\Field;

use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;
use Netmex\HydratorBundle\Options\FieldOptions;

final class FieldDefinition
{
    public function __construct(
        public string $name,
        public TransformerInterface|MapperDefinitionInterface|null $type = null,
        public mixed $value = null,
        public array $constraints = [],
        public FieldOptions $options
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): TransformerInterface|MapperDefinitionInterface|null
    {
        return $this->type;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function getOption(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function getOptions(): FieldOptions
    {
        return $this->options;
    }

    public function isValid(): bool
    {
        return true;
    }

    public function transform(): void
    {
        if (!$this->type instanceof TransformerInterface) {
            return;
        }

        $this->value = $this->type->transform($this->value);
    }
}
