<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Override;
use Symfony\Component\HttpFoundation\Response;

/** @ContentElement("bs_accordion_group_end", category="bs_accordion", template="ce_bs_accordion_group_end") */
final class AccordionGroupEndElementController extends AbstractAccordionElementController
{
    #[Override]
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
