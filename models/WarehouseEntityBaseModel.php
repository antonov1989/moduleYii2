<?php

namespace modules\warehouse\models;

use modules\warehouse\entities\Warehouse;
use siot\core\base\Model;
use siot\core\db\ActiveRecord;
use siot\general\validators\UuidValidator;
use Yii;
use yii\db\ActiveQuery;

class WarehouseEntityBaseModel extends Model
{
    public string|null $warehouse_id = null;
    public string|null $entity_type = null;
    public string|null $entity_id = null;

    public function rules(): array
    {
        return [
            ['warehouse_id', 'trim'],
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
    }

    public function attributeLabels(): array
    {
        return [
            'warehouse_id' => Yii::t('app', 'Warehouse'),
        ];
    }
}
