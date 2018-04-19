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
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $def = $container->getDefinition('siteimprove.guzzle.client');
        $def->addArgument($config['proxy_settings']['host']);
        $def->addArgument($config['proxy_settings']['port']);
        $def->addArgument($config['proxy_settings']['user'].":".$config['proxy_settings']['pass']);

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));
        if (\in_array('EzPlatformAdminUiBundle', $activatedBundles, true)) {
            $loader->load('ezadminui.yml');
        }

    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('assetic', ['bundles' => ['SiteimproveBundle']]);

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        // 1.x
        if (!\in_array('EzPlatformAdminUiBundle', $activatedBundles, true)) {
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

}
