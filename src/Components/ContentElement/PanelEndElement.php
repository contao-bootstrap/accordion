<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

final class PanelEndElement extends AbstractPanelElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs_panel_end';

    /**
     * {@inheritdoc}
     */
    protected function renderBackendView(): string
    {
        return '';
    }
}
