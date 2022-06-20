<?php

namespace Mavu\LocalBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mavu_local');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode  // @phpstan-ignore-line
            ->children()
            ->arrayNode('mavu_dummy')
            ->children()
            ->scalarNode('api_key')->defaultValue("xxxx")->info('API key for some dummy API')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
