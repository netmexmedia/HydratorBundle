<?php

namespace Netmex\HydratorBundle\Options;

use Netmex\HydratorBundle\Contracts\OptionsInterface;

final class FieldOptions implements OptionsInterface
{
    private bool $multiple = false;

    private ?string $source = null;

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }


    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getRequiredOptions(): array
    {
        return [];
    }
}