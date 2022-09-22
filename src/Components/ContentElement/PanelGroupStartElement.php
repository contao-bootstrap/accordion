<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

final class PanelGroupStartElement extends AbstractPanelElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs_panel_group_start';

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateData(array $data): array
    {
        $data = parent::prepareTemplateData($data);

        if ($data['cssID'] == '') {
            $data['cssID'] = ' id="panel-group-' . $this->get('id') . '"';
        }

        return $data;
    }
}
