<?php
namespace Mavu\GlobalBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mavu_global');
        $rootNode = $treeBuilder->getRootNode();
   
        $rootNode  // @phpstan-ignore-line
            ->children()
                ->arrayNode('tw_classes')
                    ->children()
                        ->scalarNode('npm_binary')->defaultValue("npm")->info('location of binary to run tailwind-cli with (e.g. "pnpm")')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;

    }
}
