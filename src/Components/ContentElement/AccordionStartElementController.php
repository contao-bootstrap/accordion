<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;

#[AsContentElement('bs_accordion_start', category: 'bs_accordion', template: 'ce_bs_accordion_start')]
final class AccordionStartElementController extends AbstractAccordionStartElementController
{
}
