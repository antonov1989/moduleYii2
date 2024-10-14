<?php

use siot\core\db\{
    Migration,
    MigrationTrait
};

/**
 * Handles the creation of table `warehouse_entity`.
 */
class m240216_093813_create_warehouse_entity_table extends Migration
{
    use MigrationTrait;

    /**
     * Table name
     *
     * @var string
     */
    private string $table = 'warehouse_entity';

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'warehouse_id' => $this->uuid()->notNull(),
            'entity_type' => $this->smallInteger()->notNull(),
            'entity_id' => $this->uuid()->notNull(),
        ]);

        $this->createIndex("idx-$this->table-warehouse_entity_ids", $this->table, ['warehouse_id', 'entity_type', 'entity_id'], true);

        $this->createIdx('warehouse_id');
        $this->addForeignKey(
            "fk-$this->table-warehouse_id",
            $this->table,
            'warehouse_id',
            'warehouse',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropFk('warehouse_id');
        $this->dropIdx('warehouse_id');
        $this->dropTable($this->table);
    }
}
