<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Model;
use Symfony\Component\HttpFoundation\Request;

/** @ContentElement("bs_accordion_group_start", category="bs_accordion") */
final class AccordionGroupStartElementController extends AbstractAccordionElementController
{
    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        if (empty($data['cssID'])) {
            $data['cssID'] = ' id="accordion-group-' . $model->id . '"';
        }

        return $data;
    }
}
