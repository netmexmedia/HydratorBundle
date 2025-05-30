<?php

namespace Netmex\HydratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('netmex_hydrator');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('transformers_namespace')
            ->defaultValue('App\\Transformer\\')
            ->info('Namespace where transformers are located')
            ->end()
            ->scalarNode('transformers_path')
            ->defaultValue('src/Transformer')
            ->info('Path where transformers are located')
            ->end()
            ->scalarNode('transformer_tag')
            ->defaultValue('netmex.transformer')
            ->info('Tag to apply to transformer services')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
