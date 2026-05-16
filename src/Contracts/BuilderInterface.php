<?php

namespace Netmex\HydratorBundle\Contracts;

interface BuilderInterface {
    public function add(string $field, TransformerInterface|string|null $type = null, ?array $constraints = []): static;

    public function getFields(): array;
}
