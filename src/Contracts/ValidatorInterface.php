<?php

namespace Netmex\HydratorBundle\Contracts;

use Netmex\HydratorBundle\Mapper\FieldDefinition;

interface ValidatorInterface
{
    public function validate(FieldDefinition $field, string $context): void;

    public function addViolations(string $property, string $context, array $messages): void;

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function resetErrors(): void;

}