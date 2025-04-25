<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Model;
use Override;
use Symfony\Component\HttpFoundation\Request;

/** @ContentElement("bs_accordion_group_start", category="bs_accordion", template="ce_bs_accordion_group_start") */
final class AccordionGroupStartElementController extends AbstractAccordionElementController
{
    /** {@inheritDoc} */
    #[Override]
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        if (empty($data['cssID'])) {
            $data['cssID'] = ' id="accordion-group-' . $model->id . '"';
        }

        return $data;
    }
}
