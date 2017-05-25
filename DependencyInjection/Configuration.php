<?php

namespace Siteimprove\Bundle\SiteimproveBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('siteimprove');

        $rootNode
            ->children()
                ->arrayNode('proxy_settings')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')->defaultNull()->end()
                        ->scalarNode('port')->defaultNull()->end()
                        ->scalarNode('user')->defaultNull()->end()
                        ->scalarNode('pass')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
