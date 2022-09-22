<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Model;
use Symfony\Component\HttpFoundation\Request;

/** @ContentElement("bs_panel_group_start", category="bs_panel") */
final class PanelGroupStartElementController extends AbstractPanelElementController
{
    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        if (empty($data['cssID'])) {
            $data['cssID'] = ' id="panel-group-' . $model->id . '"';
        }

        return $data;
    }
}
