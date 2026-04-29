<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Model;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Request;

use function trigger_error;

use const E_USER_DEPRECATED;

/** @deprecated Use AccordionGroupWrapperElementController (bs_accordion_group_wrapper) instead. Will be removed in a future major version. */
#[AsContentElement('bs_accordion_group_start', category: 'bs_accordion', template: 'ce_bs_accordion_group_start')]
final class AccordionGroupStartElementController extends AbstractAccordionElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
    ) {
        trigger_error(
            'Content element "bs_accordion_group_start" is deprecated.'
                . ' Use "bs_accordion_group_wrapper" instead. Will be removed in a future major version.',
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }

    /** {@inheritDoc} */
    #[Override]
    protected function prepareTemplateData(array $data, Request $request, Model $model): array
    {
        $data = parent::prepareTemplateData($data, $request, $model);

        if (empty($data['cssID'])) {
            $data['cssID'] = ' id="accordion-group-' . $model->id . '"';
        }

        return $data;
    }
}
