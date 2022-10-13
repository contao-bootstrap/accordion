<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\EventListener\Dca;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;

final class ContentDcaListener
{
    /**
     * Generate a accordion name if not given.
     *
     * @param string|null   $value         Accordion name.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @Callback(table="tl_content", target="fields.bs_accordion_name.save")
     */
    public function generateAccordionName(string|null $value, DataContainer $dataContainer): string
    {
        if (! $value && $dataContainer->activeRecord) {
            $value = 'accordion_' . $dataContainer->activeRecord->id;
        }

        return (string) $value;
    }
}
