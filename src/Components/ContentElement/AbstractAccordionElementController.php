<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Model;
use Contao\StringUtil;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Controller\ContentElement\AbstractContentElementController;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAccordionElementController extends AbstractContentElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        private readonly ColorRotate $colorRotate,
    ) {
        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker);
    }

    /** {@inheritDoc} */
    protected function preGenerate(
        Request $request,
        Model $model,
        string $section,
        array|null $classes = null,
    ): Response|null {
        if ($this->isBackendRequest($request)) {
            return $this->renderContentBackendView($model);
        }

        return parent::preGenerate($request, $model, $section, $classes);
    }

    protected function renderContentBackendView(ContentModel $model): Response
    {
        $group = $this->getAccordionGroup($model);
        $data  = [];

        if ($group && $group !== $model) {
            $data['group'] = $group->bs_accordion_name;
            $data['color'] = $this->rotateColor('ce:' . $group->id);
            $data['title'] = StringUtil::deserialize($model->headline, true)['value'] ?? '';
        } else {
            $data['color'] = $this->rotateColor('ce:' . $model->id);
            $data['group'] = $model->type === 'bs_accordion_group_start'
                ? $model->bs_accordion_name
                : $model->headline;
        }

        return $this->renderResponse('fe:be_bs_accordion', $data);
    }

    protected function getAccordionGroup(ContentModel $model): ContentModel|null
    {
        $group = ContentModel::findOneBy(
            [
                'tl_content.ptable=?',
                'tl_content.pid=?',
                '(tl_content.type = ? OR tl_content.type = ?)',
                'tl_content.sorting < ?',
            ],
            [
                $model->ptable,
                $model->pid,
                'bs_accordion_group_start',
                'bs_accordion_group_end',
                $model->sorting,
            ],
            ['order' => 'tl_content.sorting DESC'],
        );

        if ($group && $group->type === 'bs_accordion_group_start') {
            return $group;
        }

        return null;
    }

    /**
     * Rotate the color for an identifier.
     *
     * @param string $identifier The color identifier.
     */
    protected function rotateColor(string $identifier): string
    {
        return $this->colorRotate->getColor($identifier);
    }
}
