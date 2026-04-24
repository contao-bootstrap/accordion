<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Model;
use Contao\StringUtil;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('bs_accordion_wrapper', 'bs_accordion', nestedFragments: true)]
final class AccordionWrapperElementController extends AbstractContentElementController
{
    public function __construct(private readonly ColorRotate $colorRotate)
    {

    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $group = $this->getGroup($model);

        $cssId = 'accordion-' . $model->id;

        $template->set('expanded', (bool)$model->bs_expanded);
        $template->set('headingId', $cssId . '-heading');
        $template->set('collapseId', $cssId . '-collapse');
        $template->set('groupId', $group ? 'accordion-group-' . $group->id : null);
        $template->set('cssId', $cssId);

        $template->set('color', $this->colorRotate->getColor('ce:' . $model->id));
        $template->set('isBackend', $this->isBackendScope($request));
        $template->set('headline', StringUtil::deserialize($model->headline, true));

        return $template->getResponse();
    }

    private function getGroup(ContentModel $model): Model|null
    {
        return ContentModel::findOneBy(
            ['id=?', 'type=?'],
            [$model->pid, 'bs_accordion_group_wrapper']
        );
    }
}
