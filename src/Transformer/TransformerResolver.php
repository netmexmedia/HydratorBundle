<?php

namespace Netmex\HydratorBundle\Transformer;

use Netmex\HydratorBundle\Contracts\TransformerInterface;

class TransformerResolver
{
    public function resolve(string $transformer): TransformerInterface
    {
        if (!class_exists($transformer)) {
            throw new \InvalidArgumentException("Transformer class '{$transformer}' does not exist.");
        }

        $instance = new $transformer();

        if (!$instance instanceof TransformerInterface) {
            throw new \RuntimeException("Class '{$transformer}' must implement TransformerInterface.");
        }

        return $instance;
    }
}