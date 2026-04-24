<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Twig\Functions;

use Contao\ContentModel as ContaoContentModel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ContentModel extends AbstractExtension
{
    /** {@inheritDoc} */
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
