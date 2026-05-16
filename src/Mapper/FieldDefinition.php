<?php

namespace Netmex\HydratorBundle\Mapper;
use Netmex\HydratorBundle\Contracts\TransformerInterface;

class FieldDefinition
{
    public function __construct(
        public string $name,
        public ?TransformerInterface $transformer = null,
        public mixed $value = null,
        public array $constraints = []
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getTransformer(): ?TransformerInterface
    {
        return $this->transformer;
    }

    public function getValue(): mixed
    {
        return $this->value;
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
        if ($this->transformer === null) {
            return;
        }

        $this->value = $this->transformer->transform($this->getValue());
    }

    public function isRequired(): bool
    {
        return $this->constraints['required'] ?? false;
    }
}
