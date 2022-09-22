<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Symfony\Component\HttpFoundation\Response;

/** @ContentElement("bs_panel_end", category="bs_panel") */
final class PanelEndElementController extends AbstractPanelElementController
{
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
