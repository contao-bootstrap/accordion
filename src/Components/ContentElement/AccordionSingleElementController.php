<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Model;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_merge;

/** @ContentElement("bs_accordion_single", category="bs_accordion", template="ce_bs_accordion_single") */
final class AccordionSingleElementController extends AbstractAccordionStartElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
        private readonly Studio $imageStudio,
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }

    /** {@inheritDoc} */
    protected function preGenerate(
        Request $request,
        Model $model,
        string $section,
        array|null $classes = null,
    ): Response|null {
        return null;
    }

    /** {@inheritDoc} */
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        // Add an image
        if ($model->addImage && ! empty($model->singleSRC)) {
            $figure = $this->imageStudio->createFigureBuilder()
                ->from($model->singleSRC)
                ->setSize($model->size)
                ->setMetadata($model->getOverwriteMetadata())
                ->enableLightbox($model->fullsize)
                ->buildIfResourceExists();

            if ($figure) {
                $data = array_merge($data, $figure->getLegacyTemplateData(null, $model->floating));
            }
        }

        return $data;
    }
}
