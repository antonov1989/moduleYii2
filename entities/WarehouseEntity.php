<?php

namespace modules\warehouse\entities;

use siot\core\db\ActiveRecord;
use siot\general\validators\UuidValidator;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * @property string $warehouse_id [uuid]
 * @property string $entity_type
 * @property string $entity_id
 */
class WarehouseEntity extends ActiveRecord
{
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [],
                    ActiveRecord::EVENT_BEFORE_UPDATE => []
                ]
            ],
        ];
    }

    public function rules(): array
    {
        $rules = [
            ['warehouse_id', 'required'],
            ['warehouse_id', UuidValidator::class],
            [
                'warehouse_id',
                'exist',
                'targetClass' => Warehouse::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', ActiveRecord::STATUS_DELETED]);
                },
            ],

            ['entity_type', 'required'],
            ['entity_type', 'string'],

            ['entity_id', 'required'],
            ['entity_id', UuidValidator::class],
        ];

        return $rules;
    }

    public function getWarehouse(): ActiveQuery
    {
        return $this->hasOne(Warehouse::class, ['id' => 'warehouse_id']);
    }
}
