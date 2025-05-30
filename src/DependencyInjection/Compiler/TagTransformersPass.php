<?php

namespace Netmex\HydratorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TagTransformersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $transformersNamespace = $container->hasParameter('netmex_hydrator.transformers_namespace')
            ? rtrim($container->getParameter('netmex_hydrator.transformers_namespace'), '\\') . '\\'
            : 'App\\Transformer\\';

        $transformerTag = $container->hasParameter('netmex_hydrator.transformer_tag')
            ? $container->getParameter('netmex_hydrator.transformer_tag')
            : 'netmex.transformer';

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();
            if ($class && str_starts_with($class, $transformersNamespace)) {
                $definition->addTag($transformerTag);
            }
        }
    }
}
