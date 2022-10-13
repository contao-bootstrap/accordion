<?php

declare(strict_types=1);

namespace ContaoBootstrap\Accordion\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

final class PanelMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (! $schemaManager->tablesExist('tl_content')) {
            return false;
        }

        if (! $this->hasAccordionColumns() && $this->hasPanelColumns()) {
            return true;
        }

        return $this->hasPanelElements();
    }

    public function run(): MigrationResult
    {
        $this->migrateElementTypes();
        $this->migrateColumns();

        return $this->createResult(true);
    }

    private function hasPanelElements(): bool
    {
        $result = $this->connection->executeQuery(
            'SELECT count(id) FROM tl_content WHERE type IN (:types)',
            [
                'types' => [
                    'bs_panel_group_start',
                    'bs_panel_group_end',
                    'bs_panel_single',
                    'bs_panel_start',
                    'bs_panel_separator',
                    'bs_panel_end',
                ],
            ],
            ['types' => Connection::PARAM_STR_ARRAY],
        );

        return $result->fetchOne() > 0;
    }

    private function hasPanelColumns(): bool
    {
        return $this->hasColumn('bs_panel_name');
    }

    private function hasAccordionColumns(): bool
    {
        return $this->hasColumn('bs_accordion_name');
    }

    private function hasColumn(string $name): bool
    {
        $columns = $this->connection->createSchemaManager()->listTableColumns('tl_column');

        return isset($columns[$name]);
    }

    private function migrateElementTypes(): void
    {
        $this->migrateElementType('group_start');
        $this->migrateElementType('group_end');
        $this->migrateElementType('start');
        $this->migrateElementType('end');
        $this->migrateElementType('separator');
        $this->migrateElementType('single');
    }

    private function migrateElementType(string $type): void
    {
        $this->connection->executeStatement(
            'UPDATE tl_content SET type=:newType WHERE type=:oldType',
            [
                'oldType' => 'bs_panel_' . $type,
                'newType' => 'bs_accordion_' . $type,
            ],
        );
    }

    private function migrateColumns(): void
    {
        if ($this->hasAccordionColumns() || ! $this->hasPanelColumns()) {
            return;
        }

        $this->connection->executeStatement(
            'ALTER TABLE tl_content ADD bs_accordion_name VARCHAR(64) DEFAULT \'\' NOT NULL',
        );

        $this->connection->executeStatement(
            'UPDATE tl_content SET bs_accordion_name=bs_panel_name WHERE bs_panel_name != \'\'',
        );

        $this->connection->executeStatement('ALTER TABLE tl_content DROP COLUMN bs_panel_name');
    }
}
