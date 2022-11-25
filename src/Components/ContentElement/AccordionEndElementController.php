<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Symfony\Component\HttpFoundation\Response;

/** @ContentElement("bs_accordion_end", category="bs_accordion", template="ce_bs_accordion_end") */
final class AccordionEndElementController extends AbstractAccordionElementController
{
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
