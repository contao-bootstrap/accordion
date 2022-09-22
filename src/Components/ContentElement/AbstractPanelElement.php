<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\ContentModel;
use Contao\Database\Result;
use Contao\Model;
use Contao\Model\Collection;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateReference as ToolkitTemplateReference;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

abstract class AbstractPanelElement extends AbstractContentElement
{
    /**
     * Color rotate.
     *
     * @var ColorRotate
     */
    private ColorRotate $colorRotate;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private RequestScopeMatcher $scopeMatcher;

    /**
     * Define if content element should be rendered in the backend.
     *
     * @var bool
     */
    protected bool $renderInBackend = false;

    /**
     * AbstractContentElement constructor.
     *
     * @param Model|Collection|Result $model          Object model or result.
     * @param TemplateEngine          $templateEngine Template engine.
     * @param ColorRotate             $colorRotate    ColorRotate helper.
     * @param RequestScopeMatcher     $scopeMatcher   Request scope matcher.
     * @param string                  $column         Column.
     *
     * @throws InvalidArgumentException When model does not have a row method.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        ColorRotate $colorRotate,
        RequestScopeMatcher $scopeMatcher,
        string $column = 'main'
    ) {
        parent::__construct($model, $templateEngine, $column);

        $this->colorRotate  = $colorRotate;
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        if (!$this->renderInBackend && $this->isBackendRequest()) {
            return $this->renderBackendView();
        }

        return parent::generate();
    }

    /**
     * Render the backend view.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function renderBackendView(): string
    {
        $group = $this->getPanelGroup();
        $data  = [];

        if ($group && $group !== $this->getModel()) {
            $data['group'] = $group->bs_panel_name;
            $data['color'] = $this->rotateColor('ce:' . $group->id);
            $data['title'] = $this->get('headline');
        } else {
            $data['color'] = $this->rotateColor('ce:' . $this->get('id'));
            $data['group'] = $this->get('type') === 'bs_panel_group_start'
                ? $this->get('bs_panel_name')
                : $this->get('headline');
        }

        return $this->render(
            new ToolkitTemplateReference(
                'be_bs_panel',
                'html5',
                ToolkitTemplateReference::SCOPE_FRONTEND
            ),
            $data
        );
    }

    /**
     * Check if we are in backend mode.
     *
     * @return bool
     */
    protected function isBackendRequest(): bool
    {
        return $this->scopeMatcher->isBackendRequest();
    }

    /**
     * Get the panel group starting element.
     *
     * @return ContentModel|null
     */
    protected function getPanelGroup(): ?ContentModel
    {
        $group = ContentModel::findOneBy(
            [
                'tl_content.ptable=?',
                'tl_content.pid=?',
                '(tl_content.type = ? OR tl_content.type = ?)',
                'tl_content.sorting < ?',
            ],
            [
                $this->get('ptable'),
                $this->get('pid'),
                'bs_panel_group_start',
                'bs_panel_group_end',
                $this->get('sorting'),
            ],
            [
                'order' => 'tl_content.sorting DESC',
            ]
        );

        if ($group && $group->type === 'bs_panel_group_start') {
            return $group;
        }

        return null;
    }

    /**
     * Rotate the color for an identifier.
     *
     * @param string $identifier The color identifier.
     *
     * @return string
     */
    protected function rotateColor(string $identifier): string
    {
        return $this->colorRotate->getColor($identifier);
    }
}
