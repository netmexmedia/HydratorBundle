<?php

namespace Netmex\HydratorBundle\Options;

use Netmex\HydratorBundle\Contracts\OptionsInterface;

final class MapperOptions implements OptionsInterface
{
    private string $model;
    private bool $multiple  = false;

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getRequiredOptions(): array
    {
        return ['model'];
    }
}