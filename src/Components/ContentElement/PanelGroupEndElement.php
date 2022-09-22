<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

final class PanelGroupEndElement extends AbstractPanelElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs_panel_group_end';

    /**
     * {@inheritdoc}
     */
    protected function renderBackendView(): string
    {
        return '';
    }
}
