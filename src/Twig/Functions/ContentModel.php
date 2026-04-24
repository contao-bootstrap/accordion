<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Twig\Functions;

use Contao\ContentModel as ContaoContentModel;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ContentModel extends AbstractExtension
{
    /** {@inheritDoc} */
    #[Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_model', $this->getContentModel(...)),
        ];
    }

    public function getContentModel(int $id): ContaoContentModel|null
    {
        return ContaoContentModel::findByPk($id);
    }
}
