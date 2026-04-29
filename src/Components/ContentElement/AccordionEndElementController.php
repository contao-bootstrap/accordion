<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Components\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use ContaoBootstrap\Core\Helper\ColorRotate;
use Netzmacht\Contao\Toolkit\Response\ResponseTagger;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\HttpFoundation\Response;

use function trigger_error;

use const E_USER_DEPRECATED;

/** @deprecated Use AccordionWrapperElementController (bs_accordion_wrapper) instead. Will be removed in a future major version. */
#[AsContentElement('bs_accordion_end', category: 'bs_accordion', template: 'ce_bs_accordion_end')]
final class AccordionEndElementController extends AbstractAccordionElementController
{
    public function __construct(
        TemplateRenderer $templateRenderer,
        RequestScopeMatcher $scopeMatcher,
        ResponseTagger $responseTagger,
        TokenChecker $tokenChecker,
        ColorRotate $colorRotate,
    ) {
        trigger_error(
            'Content element "bs_accordion_end" is deprecated. Use "bs_accordion_wrapper" instead.'
                . ' Will be removed in a future major version.',
            E_USER_DEPRECATED,
        );

        parent::__construct($templateRenderer, $scopeMatcher, $responseTagger, $tokenChecker, $colorRotate);
    }

    #[Override]
    protected function renderContentBackendView(ContentModel $model): Response
    {
        return new Response();
    }
}
