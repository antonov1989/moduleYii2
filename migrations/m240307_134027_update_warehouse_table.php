<?php

use modules\core\enums\ModuleTypeEnum;
use siot\core\db\{
    Migration,
    MigrationTrait,
};

/**
 * Class m240307_134027_update_warehouse_table
 */
class m240307_134027_update_warehouse_table extends Migration
{
    use MigrationTrait;

    private string $table = 'warehouse';

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->execute("INSERT INTO warehouse_entity (warehouse_id, entity_type, entity_id)
            SELECT id, '" . ModuleTypeEnum::company->value . "', company_id FROM warehouse
            WHERE NOT EXISTS (SELECT 1 FROM warehouse_entity WHERE warehouse_entity.warehouse_id = warehouse.id)");

        $this->dropFk('company_id');
        $this->dropIdx('company_id');

        $this->dropColumn($this->table, 'company_id');
        $this->dropColumn($this->table, 'company_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->addColumn($this->table, 'company_id', $this->uuid());
        $this->addColumn($this->table, 'company_type', $this->smallInteger()->notNull()->defaultValue(1));

        $this->createIdx('company_id');
        $this->addFk('company_id');
    }
}
