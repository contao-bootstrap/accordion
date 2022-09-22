<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ContaoBootstrap\Core\ContaoBootstrapCoreBundle;
use ContaoBootstrap\Panel\ContaoBootstrapPanelBundle;

final class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        $bundleConfig = BundleConfig::create(ContaoBootstrapPanelBundle::class)
            ->setLoadAfter([ContaoCoreBundle::class, ContaoBootstrapCoreBundle::class]);

        return [$bundleConfig];
    }
}
