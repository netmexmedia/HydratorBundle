<?php

namespace Netmex\HydratorBundle\Contracts;

interface TransformerInterface
{
    public function transform(string $data): mixed;

    public function reverseTransform(string $data): string;

}