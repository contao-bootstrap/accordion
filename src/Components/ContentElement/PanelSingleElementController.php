<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\Components\ContentElement;

use Contao\Controller;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Model;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_merge;
use function is_file;

/** @ContentElement("bs_panel_single", category="bs_panel") */
final class PanelSingleElementController extends AbstractPanelStartElementController
{
    private RepositoryManager $repositoryManager;

    private string $projectDir;

    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
        RepositoryManager $repositoryManager,
        string $projectDir
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);

        $this->repositoryManager = $repositoryManager;
        $this->projectDir        = $projectDir;
    }

    /** {@inheritDoc} */
    protected function preGenerate(Request $request, Model $model, string $section, ?array $classes = null): ?Response
    {
        return null;
    }

    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        // Add an image
        if ($model->addImage && ! empty($model->singleSRC)) {
            $repository = $this->repositoryManager->getRepository(FilesModel::class);
            $fileModel  = $repository->findByUuid($model->singleSRC);

            if ($fileModel !== null && is_file($this->projectDir . '/' . $fileModel->path)) {
                $data['singleSRC'] = $fileModel->path;

                $template = new FrontendTemplate();
                Controller::addImageToTemplate($template, $data, null, null, $fileModel);

                $data = array_merge($data, $template->getData());
            }
        }

        return $data;
    }
}
