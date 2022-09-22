<?php

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\ContentModel;
use Contao\Database\Result;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

final class PanelElementFactory implements ComponentFactory
{
    /**
     * Panel element types.
     *
     * @var array
     */
    private array $panelElementTypes = [
        'bs_panel_group_start' => PanelGroupStartElement::class,
        'bs_panel_group_end'   => PanelGroupEndElement::class,
        'bs_panel_start'       => PanelStartElement::class,
        'bs_panel_end'         => PanelEndElement::class,
        'bs_panel_single'      => PanelSingleElement::class,
    ];

    /**
     * Template engine.
     *
     * @var TemplateEngine
     */
    private TemplateEngine $templateEngine;

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
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private RepositoryManager $repositoryManager;

    /**
     * PanelElementFactory constructor.
     *
     * @param TemplateEngine      $templateEngine    The template engine.
     * @param ColorRotate         $colorRotate       Color rotate service.
     * @param RequestScopeMatcher $scopeMatcher      Request scope matcher.
     * @param RepositoryManager   $repositoryManager Repository manager.
     */
    public function __construct(
        TemplateEngine $templateEngine,
        ColorRotate $colorRotate,
        RequestScopeMatcher $scopeMatcher,
        RepositoryManager $repositoryManager
    ) {
        $this->templateEngine    = $templateEngine;
        $this->colorRotate       = $colorRotate;
        $this->scopeMatcher      = $scopeMatcher;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model) : bool
    {
        if (!$model instanceof ContentModel && !($model instanceof Result)) {
            return false;
        }

        return isset($this->panelElementTypes[$model->type]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ComponentNotFound If an unsupported element type is given.
     */
    public function create($model, string $column) : Component
    {
        if (!isset($this->panelElementTypes[$model->type])) {
            throw ComponentNotFound::forModel($model);
        }

        $className = $this->panelElementTypes[$model->type];

        if ($className === PanelSingleElement::class) {
            return new $className(
                $model,
                $this->templateEngine,
                $this->colorRotate,
                $this->scopeMatcher,
                $this->repositoryManager,
                $column
            );
        }

        return new $className(
            $model,
            $this->templateEngine,
            $this->colorRotate,
            $this->scopeMatcher,
            $column
        );
    }
}
