<?php

use modules\core\enums\ModuleTypeEnum;
use \modules\warehouse\entities\Warehouse;
use siot\core\db\{
    Migration,
    MigrationTrait,
};

/**
 * Class m240312_100849_update_warehouse_table_change_type_column
 */
class m240312_100849_update_warehouse_table_change_type_column extends Migration
{
    use MigrationTrait;

    private string $table = 'warehouse';
    private string $warehouseTypeId = 'd9cbc3aa-09fc-46c0-94ea-b724709e1117';

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->dropColumn($this->table, 'type');

        $this->execute("INSERT INTO warehouse_type (id, name, module_type, status)
            VALUES ('" . $this->warehouseTypeId . "', 'Default type', '" . ModuleTypeEnum::company->value . "', " . Warehouse::STATUS_ACTIVE. ")");

        $this->addColumn($this->table, 'type_id', $this->uuid()->after('id'));
        $this->execute("UPDATE warehouse SET type_id = '" . $this->warehouseTypeId . "'");
        $this->alterColumn($this->table, 'type_id', $this->uuid()->notNull());

        $this->createIdx('type_id');
        $this->addFk('type_id', 'warehouse_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropFk('type_id');
        $this->dropIdx('type_id');

        $this->dropColumn($this->table, 'type_id');

        $this->addColumn($this->table, 'type', $this->smallInteger()->notNull()->defaultValue(1)->after('id'));

        $this->execute("DELETE FROM warehouse_type WHERE id = '" . $this->warehouseTypeId . "'");
    }
}
