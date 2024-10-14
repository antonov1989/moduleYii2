<?php

namespace modules\warehouse\entities\validators;

use modules\warehouse\entities\Warehouse;
use yii\validators\Validator;
use Yii;

class UniqueNamePerEntityTypeValidator extends Validator
{
    public function validateAttribute($model, $attribute): void
    {
        $existingWarehouse = Warehouse::find()
            ->innerJoinWith('warehouseEntity')
            ->where(['=', 'LOWER(name)', strtolower($model->name)])
            ->andWhere(['!=', 'status', Warehouse::STATUS_DELETED])
            ->andFilterWhere(['warehouse_entity.entity_type' => $model->entity_type ?? null])
            ->andFilterWhere(['!=', 'warehouse.id', $model->id ?? null])
            ->exists();

        if ($existingWarehouse) {
            $this->addError($model, $attribute, Yii::t('app', '{attribute} "{value}" has already been taken.', [
                'attribute' => $model->getAttributeLabel($attribute),
                'value' => $model->$attribute,
            ]));
        }
    }
}
