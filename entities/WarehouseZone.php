<?php

namespace modules\warehouse\entities;

use modules\commonTm\entities\BaseActiveRecord;
use Iwms\Core\General\Interfaces\DisplayedNameInterface;
use siot\general\settings\Fields;
use siot\general\validators\UuidValidator;
use yii\db\ActiveQuery;

/**
 * Class WarehouseZone
 * @package common\models
 *
 * @property int $id [integer]
 * @property string $warehouse_id [uuid]
 * @property string $name [varchar(255)]
 * @property int $status [integer]
 * @property int $parent_id [integer]
 * @property string $parent_path [ltree]
 * @property int $created_at [integer]
 * @property int $updated_at [integer]
 * @property int $deleted_at [integer]
 *
 * @property Warehouse $warehouse
 */
class WarehouseZone extends BaseActiveRecord implements DisplayedNameInterface
{
    /**
     * Scenarios
     *
     * @var string
     */
    const SCENARIO_CHANGE_NAME = 'changeNameScenario';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['id', 'integer', 'max' => Fields::INT_MAX],
            ['id', 'unique'],

            ['warehouse_id', 'required'],
            ['warehouse_id', UuidValidator::class],
            [
                'warehouse_id',
                'exist',
                'targetClass' => Warehouse::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status' , Warehouse::STATUS_DELETED]);
                }
            ],

            ['name', 'required'],
            ['name', 'string', 'max' => Fields::VARCHAR_LENGTH],
            [
                'name',
                'unique',
                'filter' => function (ActiveQuery $query): void {
                    $query
                        ->andWhere(['warehouse_id' => $this->warehouse_id])
                        ->andWhere(['!=', 'status' , Warehouse::STATUS_DELETED]);
                }
            ],

            ['parent_id', 'integer', 'max' => Fields::INT_MAX],
            [
                'parent_id',
                'exist',
                'targetAttribute' => 'id',
                'message' => 'Zone not found.'
            ],

            ['status', 'in', 'range' => self::getStatuses()],
            ['status', 'default', 'value' => self::STATUS_ACTIVE]
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CHANGE_NAME] = ['name'];

        return $scenarios;
    }

    /**
     * @return ActiveQuery
     */
    public function getWarehouse(): ActiveQuery
    {
        return $this->hasOne(Warehouse::class, ['id' => 'warehouse_id']);
    }

    public function getDisplayedName(): string
    {
        return $this->name;
    }
}
