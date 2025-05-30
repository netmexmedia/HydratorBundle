<?php

namespace Netmex\HydratorBundle;

use Netmex\HydratorBundle\DependencyInjection\Compiler\TagTransformersPass;
use Netmex\HydratorBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Netmex\HydratorBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HydratorBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TagTransformersPass());
    }

    public function loadExtension(array $configs, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $projectDir = $builder->getParameter('kernel.project_dir');

        $transformersPathConfig = $config['transformers_path'];
        $transformersNamespace = $config['transformers_namespace'];
        $transformerTag = $config['transformer_tag'];

        if (!str_starts_with($transformersPathConfig, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Z]:\\\\/', $transformersPathConfig)) {
            $transformersPath = $projectDir . DIRECTORY_SEPARATOR . trim($transformersPathConfig, '/\\');
        } else {
            $transformersPath = $transformersPathConfig;
        }

        $builder->setParameter('netmex_hydrator.transformers_path', $transformersPath);
        $builder->setParameter('netmex_hydrator.transformers_namespace', $transformersNamespace);
        $builder->setParameter('netmex_hydrator.transformer_tag', $transformerTag);

        $container->import(__DIR__.'/../config/services.yaml');

        $container->services()
            ->load($transformersNamespace, $transformersPath)
            ->autowire()
            ->autoconfigure();
    }

}