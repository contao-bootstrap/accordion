<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\ContentModel;
use Contao\Model;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;

use function is_string;

abstract class AbstractPanelStartElementController extends AbstractPanelElementController
{
    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $cssId = $data['cssId'] ?? 'panel-' . $model->id;

        $data['expanded']   = (bool) $model->bs_expanded;
        $data['headingId']  = $cssId . '-heading';
        $data['collapseId'] = $cssId . '-collapse';
        $data['groupId']    = $this->getGroupId($model);

        return $data;
    }

    /**
     * Get the panel group id.
     */
    private function getGroupId(ContentModel $model): ?string
    {
        $group = $this->getPanelGroup($model);
        if (! $group) {
            return null;
        }

        $cssID = StringUtil::deserialize($group->cssID, true);
        if (is_string($cssID[0]) && $cssID[0] !== '') {
            return $cssID[0];
        }

        return 'panel-group-' . $group->id;
    }
}
