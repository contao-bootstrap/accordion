<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Override;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('bs_accordion_end', category: 'bs_accordion', template: 'ce_bs_accordion_end')]
final class AccordionEndElementController extends AbstractAccordionElementController
{
    #[Override]
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
