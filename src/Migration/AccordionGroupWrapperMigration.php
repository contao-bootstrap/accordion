<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

use Override;

final class AccordionGroupWrapperMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection, private readonly bool $enableMigration = false)
    {
    }

    #[Override]
    public function shouldRun(): bool
    {
        if ($this->enableMigration === false) {
            return false;
        }

        $schemaManager = $this->connection->createSchemaManager();
        if (! $schemaManager->tablesExist(['tl_content'])) {
            return false;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
                ->select('COUNT(tc.id) as count')
                ->from('tl_content', 'tc')
                ->where($queryBuilder->expr()->eq('tc.type', ':type'))
                ->setParameter('type', 'bs_accordion_group_start')
                ->executeQuery()
                ->fetchOne() > 0;
    }

    #[Override]
    public function run(): MigrationResult
    {
        $sql = <<<SQL
    SELECT
        accordion_start.id        AS accordion_start_id,
        accordion_start.pid       AS pid,
        accordion_start.ptable    AS ptable,
        el.id                AS element_id,
        el.type              AS element_type,
        el.sorting           AS element_sorting,
        (
            SELECT accordion_stop.id
            FROM tl_content accordion_stop
            WHERE accordion_stop.pid     = accordion_start.pid
              AND accordion_stop.ptable  = accordion_start.ptable
              AND accordion_stop.type    = 'bs_accordion_group_end'
              AND accordion_stop.sorting > accordion_start.sorting
            ORDER BY accordion_stop.sorting ASC
            LIMIT 1
        )                    AS accordion_stop_id
    FROM tl_content accordion_start
    LEFT JOIN tl_content el
        ON  el.pid     = accordion_start.pid
        AND el.ptable  = accordion_start.ptable
        AND el.sorting > accordion_start.sorting
        AND el.sorting < (
            SELECT MIN(accordion_stop.sorting)
            FROM tl_content accordion_stop
            WHERE accordion_stop.pid     = accordion_start.pid
              AND accordion_stop.ptable  = accordion_start.ptable
              AND accordion_stop.type    = 'bs_accordion_group_end'
              AND accordion_stop.sorting > accordion_start.sorting
        )
    WHERE accordion_start.type = 'bs_accordion_group_start'
    ORDER BY accordion_start.pid, accordion_start.ptable, accordion_start.sorting, el.sorting
SQL;

        $accordionGroups = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $groupContainers = array_reduce($accordionGroups, function (array $carry, array $row) {
            $startId = $row['accordion_start_id'];

            $carry[$startId] ??= [
                'start_id' => $startId,
                'stop_id'  => $row['accordion_stop_id'],
                'elements' => [],
            ];

            if ($row['element_id'] !== null) {
                $carry[$startId]['elements'][] = $row;
            }

            return $carry;
        }, []);

        $elementCount = array_sum(
            array_map(
                static fn (array $gridContainer) => count($gridContainer['elements']),
                $groupContainers
            )
        );

        foreach ($groupContainers as $gridContainer) {
            $this->connection->update(
                'tl_content',
                ['type' => 'bs_accordion_group_wrapper'],
                ['id' => $gridContainer['start_id']]
            );

            foreach ($gridContainer['elements'] as $element) {
                $this->connection->update(
                    'tl_content',
                    ['pid' => $gridContainer['start_id'], 'ptable' => 'tl_content'],
                    ['id' => $element['element_id']]
                );
            }

            $this->connection->delete('tl_content', ['id' => $gridContainer['stop_id']]);
        }

        return $this->createResult(
            true,
            'Migrated ' . count($groupContainers) . ' grid containers and ' . $elementCount . ' elements.'
        );
    }
}
