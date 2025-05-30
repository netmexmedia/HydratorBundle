<?php

namespace Netmex\HydratorBundle\Options;

class OptionsResolver
{
    private array $defaults = [];

    private array $resolved = [];

    public function resolve(array $options = []): array
    {
        $clone = clone $this;
        $clone->resolved = $this->defaults;

        foreach ($options as $option => $value) {
            $clone->resolved['fields'][$option] = $value;
        }

        return $clone->resolved;
    }

    public function setDefaults(array $defaults): static
    {
        foreach ($defaults as $option => $value) {
            $this->setDefault($option, $value);
        }

        return $this;
    }

    public function setDefault(string $option, mixed $value): static
    {
        $this->defaults[$option] = $value;

        return $this;
    }

    public function hasModel(): bool
    {
        return isset($this->defaults['model']);
    }
}