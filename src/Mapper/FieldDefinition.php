<?php

namespace Netmex\HydratorBundle\Mapper;
use Netmex\HydratorBundle\Contracts\MapperDefinitionInterface;
use Netmex\HydratorBundle\Contracts\TransformerInterface;

class FieldDefinition
{
    public function __construct(
        public string $name,
        public TransformerInterface|MapperDefinitionInterface|null $type = null,
        public mixed $value = null,
        public array $constraints = []
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

    public function isRequired(): bool
    {
        return $this->constraints['required'] ?? false;
    }
}
