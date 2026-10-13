<?php

namespace Netmex\HydratorBundle\Contracts;

interface BuilderInterface
{
    /**
     * @param string $field
     * @param TransformerInterface|MapperDefinitionInterface|string|null $type
     * @param array|null $constraints
     * @param array{
     *     multiple?: bool,
     *     source?: string|null
     * } $options
     */
    public function add(string $field, TransformerInterface|string|MapperDefinitionInterface $type = null, ?array $constraints = [], array $options = []): static;

    public function getFields(): array;
}
