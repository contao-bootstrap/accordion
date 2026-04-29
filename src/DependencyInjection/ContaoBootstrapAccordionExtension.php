<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\DependencyInjection;

use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class ContaoBootstrapAccordionExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'contao_bootstrap.accordion.enable_wrapper_migration',
            $config['enable_wrapper_migration'],
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('listener.yaml');
        $loader->load('services.yaml');

        if ($config['enable_legacy_elements']) {
            $loader->load('legacy.yaml');
        }
    }
}
