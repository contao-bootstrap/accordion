<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use ContaoBootstrap\Accordion\ContaoBootstrapAccordionBundle;
use ContaoBootstrap\Core\ContaoBootstrapCoreBundle;
use Override;

final class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getBundles(ParserInterface $parser): array
    {
        $bundleConfig = BundleConfig::create(ContaoBootstrapAccordionBundle::class)
            ->setLoadAfter([ContaoCoreBundle::class, ContaoBootstrapCoreBundle::class]);

        return [$bundleConfig];
    }
}
