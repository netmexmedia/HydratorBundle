<?php

namespace Netmex\HydratorBundle\Exception;

use InvalidArgumentException;

final class InvalidMappingInputException extends InvalidArgumentException
{
    public static function expectedList(): self
    {
        return new self(
            'A multiple mapper expects a list of items.',
        );
    }

    public static function expectedArrayItem(): self
    {
        return new self(
            'Each item in a multiple mapper must be an array.',
        );
    }
}
