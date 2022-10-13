<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\Model;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;

use function is_string;

abstract class AbstractAccordionStartElementController extends AbstractAccordionElementController
{
    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $cssId = $data['cssId'] ?? 'accordion-' . $model->id;

        $data['expanded']   = (bool) $model->bs_expanded;
        $data['headingId']  = $cssId . '-heading';
        $data['collapseId'] = $cssId . '-collapse';
        $data['groupId']    = $this->getGroupId($model);

        return $data;
    }

    /**
     * Get the accordion group id.
     */
    private function getGroupId(ContentModel $model): string|null
    {
        $group = $this->getAccordionGroup($model);
        if (! $group) {
            return null;
        }

        $cssID = StringUtil::deserialize($group->cssID, true);
        if (is_string($cssID[0]) && $cssID[0] !== '') {
            return $cssID[0];
        }

        return 'accordion-group-' . $group->id;
    }
}
