<?php

namespace Siteimprove\Bundle\SiteimproveBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Class SiteimproveExtension.
 */
class SiteimproveExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $def = $container->getDefinition('siteimprove.guzzle.client');
        $def->addArgument($config['proxy_settings']['host']);
        $def->addArgument($config['proxy_settings']['port']);
        $def->addArgument($config['proxy_settings']['user'].":".$config['proxy_settings']['pass']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('assetic', ['bundles' => ['SiteimproveBundle']]);

        $container->setParameter(
            'siteimprove_platformui.public_dir',
            'bundles/siteimprove'
        );
        $yuiConfigFile = __DIR__.'/../Resources/config/yui.yml';
        $config        = Yaml::parse(file_get_contents($yuiConfigFile));
        $container->prependExtensionConfig('ez_platformui', $config);
        $container->addResource(new FileResource($yuiConfigFile));
    }

}
