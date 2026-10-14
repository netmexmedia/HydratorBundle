<?php

namespace Netmex\HydratorBundle\Exception;

use InvalidArgumentException;

final class InvalidNestedMappingInputException extends InvalidArgumentException
{
    public static function expectedList(string $fieldName): self
    {
        return new self(sprintf(
            'Field "%s" expects a list of items.',
            $fieldName,
        ));
    }

    public static function expectedObjectData(string $fieldName): self
    {
        return new self(sprintf(
            'Field "%s" expects an array of object data.',
            $fieldName,
        ));
    }

    public static function expectedArrayItem(string $fieldName): self
    {
        return new self(sprintf(
            'Each item in field "%s" must be an array.',
            $fieldName,
        ));
    }
}
