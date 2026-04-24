<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('bs_accordion_group_wrapper', 'bs_accordion', nestedFragments: true)]
final class AccordionGroupWrapperElementController extends AbstractContentElementController
{
    public function __construct(private readonly ColorRotate $colorRotate)
    {

    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->group     = $model->bs_accordion_name ?? $model->headline;
        $template->color     = $this->colorRotate->getColor('ce:' . $model->id);
        $template->isBackend = $this->isBackendScope($request);
        $template->groupId   = 'accordion-group-' . $model->id ;


        return $template->getResponse();
    }
}
