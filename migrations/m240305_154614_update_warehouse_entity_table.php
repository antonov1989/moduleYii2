<?php

use modules\core\enums\{
    EntityTypeEnum,
    ModuleTypeEnum,
};
use siot\core\db\{
    Migration,
    MigrationTrait,
};

/**
 * Class m240305_154614_update_warehouse_entity_table
 */
class m240305_154614_update_warehouse_entity_table extends Migration
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
        $this->alterColumn($this->table, 'entity_type', $this->string()->notNull());
        $this->execute('UPDATE ' . $this->table . ' SET entity_type = \'' . ModuleTypeEnum::customer->value . '\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->execute('UPDATE ' . $this->table . ' SET entity_type = ' . EntityTypeEnum::entityTypeCustomer->value);
        $this->execute('ALTER TABLE ' . $this->table . ' ALTER COLUMN entity_type TYPE smallint USING entity_type::smallint');
    }
}
