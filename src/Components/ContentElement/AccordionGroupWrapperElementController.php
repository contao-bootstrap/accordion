<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('bs_accordion_group_wrapper', 'bs_accordion', nestedFragments: true)]
final class AccordionGroupWrapperElementController extends AbstractContentElementController
{
    public function __construct(private readonly ColorRotate $colorRotate)
    {
    }

    #[Override]
    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->isBackendScope($request)) {
            $template->setName('backend/accordion_wildcard');

            $template->set('title', $this->getTitle($model));
            $template->set('color', $this->colorRotate->getColor('ce:' . $model->id));

            return $template->getResponse();
        }

        $template->set('groupId', 'accordion-group-' . $model->id);

        return $template->getResponse();
    }

    private function getTitle(ContentModel $model): string
    {
        $headline = StringUtil::deserialize($model->headline, true);

        return $model->bs_accordion_name ?: $headline['value'] ?? '';
    }
}
