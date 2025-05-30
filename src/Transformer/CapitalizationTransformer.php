<?php

namespace Netmex\HydratorBundle\Transformer;

use Netmex\HydratorBundle\Contracts\TransformerInterface;

class CapitalizationTransformer implements TransformerInterface
{
    public function transform(string $data): mixed
    {
        return strtoupper($data);
    }

    public function reverseTransform(string $data): string
    {
        return strtolower($data);
    }
}