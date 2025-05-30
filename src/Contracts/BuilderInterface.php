<?php

namespace Netmex\HydratorBundle\Contracts;

interface BuilderInterface {
    public function add(string $field, TransformerInterface|string $type, ?array $constraints): static;

    public function getFields(): array;
}