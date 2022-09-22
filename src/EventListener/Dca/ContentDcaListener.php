<?php

declare(strict_types=1);

namespace ContaoBootstrap\Panel\EventListener\Dca;

use Contao\ContentModel;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;

use function sprintf;

final class ContentDcaListener
{
    private ContaoFramework $framework;

    /**
     * Content repository.
     *
     * @var Adapter<ContentModel>
     */
    private Adapter $repository;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework  = $framework;
        $this->repository = $this->framework->getAdapter(ContentModel::class);
    }

    /**
     * Get the panel group options.
     *
     * @param DataContainer|null $dataContainer Data container driver.
     *
     * @return array<int|string,string>
     */
    public function panelGroupOptions($dataContainer = null): array
    {
        $columns[] = 'tl_content.type = ?';
        $values[]  = 'bs_panel_group_start';

        if ($dataContainer) {
            $columns[] = 'tl_content.pid = ?';
            $columns[] = 'tl_content.ptable = ?';
            $columns[] = 'tl_content.sorting < ?';

            $values[] = $dataContainer->activeRecord->pid;
            $values[] = $dataContainer->activeRecord->ptable;
            $values[] = $dataContainer->activeRecord->sorting;
        }

        $collection = $this->repository->findBy($columns, $values, ['order' => 'tl_content.sorting ASC']);
        $options    = [];

        if ($collection) {
            foreach ($collection as $model) {
                $options[$model->id] = sprintf(
                    '%s [%s]',
                    $model->bs_panel_name,
                    $model->id
                );
            }
        }

        return $options;
    }

    /**
     * Generate a panel name if not given.
     *
     * @param string|null   $value         Panel name.
     * @param DataContainer $dataContainer Data container driver.
     */
    public function generatePanelName(?string $value, DataContainer $dataContainer): string
    {
        if (! $value) {
            $value = 'panel_' . $dataContainer->activeRecord->id;
        }

        return $value;
    }
}
