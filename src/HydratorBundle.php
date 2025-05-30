<?php

namespace Netmex\HydratorBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HydratorBundle extends AbstractBundle
{
    public function loadExtension(array $configs, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $transformersPath = $config['transformers_path'];
        $transformerTag = $config['transformer_tag'];

        $builder->setParameter('netmex_hydrator.transformers_path', $transformersPath);
        $builder->setParameter('netmex_hydrator.transformer_tag', $transformerTag);

        $container->services()
            ->load('App\\Transformer\\', $transformersPath)
            ->autowire()
            ->autoconfigure()
            ->tag($transformerTag);
    }
}