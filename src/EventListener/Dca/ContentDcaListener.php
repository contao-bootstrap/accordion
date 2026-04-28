<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\EventListener\Dca;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

final class ContentDcaListener
{
    #[AsCallback(table: 'tl_content', target: 'fields.bs_accordion_name.save')]
    public function generateAccordionName(string|null $value, DataContainer $dataContainer): string
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (! $value && $dataContainer->activeRecord) {
            $value = 'accordion_' . $dataContainer->activeRecord->id;
        }

        return (string) $value;
    }
}
