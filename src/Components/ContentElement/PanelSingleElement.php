<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\Controller;
use Contao\Database\Result;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Model;
use Contao\Model\Collection;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Symfony\Component\Templating\EngineInterface as TemplateEngine;

use function array_merge;
use function is_file;

final class PanelSingleElement extends AbstractPanelStartElement
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $templateName = 'ce_bs_panel_single';

    /**
     * Repository manager.
     *
     * @var RepositoryManager
     */
    private RepositoryManager $repositoryManager;

    /**
     * AbstractContentElement constructor.
     *
     * @param Model|Collection|Result $model             Object model or result.
     * @param TemplateEngine          $templateEngine    Template engine.
     * @param ColorRotate             $colorRotate       ColorRotate helper.
     * @param RequestScopeMatcher     $scopeMatcher      Request scope matcher.
     * @param RepositoryManager       $repositoryManager Repository manager.
     * @param string                  $column            Column.
     *
     * @throws InvalidArgumentException When model does not have a row method.
     */
    public function __construct(
        $model,
        TemplateEngine $templateEngine,
        ColorRotate $colorRotate,
        RequestScopeMatcher $scopeMatcher,
        RepositoryManager $repositoryManager,
        string $column = 'main'
    ) {
        parent::__construct($model, $templateEngine, $colorRotate, $scopeMatcher, $column);

        $this->repositoryManager = $repositoryManager;
        $this->renderInBackend   = true;
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareTemplateData(array $data) : array
    {
        $data = parent::prepareTemplateData($data);

        // Add an image
        if ($this->get('addImage') && $this->get('singleSRC') != '') {
            /** @var FilesModel $repository */
            $repository = $this->repositoryManager->getRepository(FilesModel::class);
            $fileModel  = $repository->findByUuid($this->get('singleSRC'));

            if ($fileModel !== null && is_file(TL_ROOT . '/' . $fileModel->path)) {
                $data['singleSRC'] = $fileModel->path;

                $template = new FrontendTemplate($this->templateName);
                Controller::addImageToTemplate($template, $data, null, null, $fileModel);

                $data = array_merge($data, $template->getData());
            }
        }

        return $data;
    }
}
