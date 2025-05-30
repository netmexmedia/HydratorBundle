<?php

namespace Netmex\HydratorBundle\Registry;

use Netmex\HydratorBundle\Contracts\TransformerInterface;
use Psr\Container\ContainerInterface;

class TransformerLocator
{
    public function __construct(
        private ContainerInterface $locator
    ) {}

    public function get(string $class): TransformerInterface
    {
        if (!$this->locator->has($class)) {
            throw new \InvalidArgumentException("Transformer '$class' not found in locator.");
        }

        return $this->locator->get($class);
    }
}
