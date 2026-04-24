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
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('bs_accordion_wrapper', 'bs_accordion', nestedFragments: true)]
final class AccordionWrapperElementController extends AbstractContentElementController
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

        $group = $this->getGroup($model);
        $cssId = 'accordion-' . $model->id;

        $template->set('expanded', (bool) $model->bs_expanded);
        $template->set('headingId', $cssId . '-heading');
        $template->set('collapseId', $cssId . '-collapse');
        $template->set('groupId', $group ? 'accordion-group-' . $group->id : null);
        $template->set('cssId', $cssId);

        return $template->getResponse();
    }

    private function getGroup(ContentModel $model): Model|null
    {
        return ContentModel::findOneBy(
            ['id=?', 'type=?'],
            [$model->pid, 'bs_accordion_group_wrapper'],
        );
    }

    private function getTitle(ContentModel $model): string
    {
        $headline = StringUtil::deserialize($model->headline, true);

        return $headline['value'] ?? '';
    }
}
