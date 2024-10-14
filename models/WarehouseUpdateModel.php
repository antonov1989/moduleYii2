<?php

namespace modules\warehouse\models;

use Iwms\Core\Group\Entities\Group;
use modules\commonTm\models\BaseModel;
use modules\group\enums\GroupType;
use modules\warehouse\entities\validators\UniqueNamePerEntityTypeValidator;
use modules\warehouse\entities\Warehouse;
use Iwms\Core\WarehouseType\Entities\WarehouseType;
use siot\core\db\ActiveRecord;
use siot\general\settings\Fields;
use siot\general\validators\UuidValidator;
use Yii;
use yii\db\ActiveQuery;

class WarehouseUpdateModel extends BaseModel
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $type_id= null;
    public ?string $email = null;
    public ?string $address = null;
    public ?string $zip = null;
    public ?string $city = null;
    public ?string $number = null;
    public int|string|null $group_id = null;

    public ?string $entity_type = null;
    public ?string $entity_id = null;

    public function rules(): array
    {
        return [
            ['id', 'safe'],

            ['name', 'required'],
            ['name', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['name', UniqueNamePerEntityTypeValidator::class],

            ['type_id', 'required'],
            ['type_id', 'trim'],
            ['type_id', UuidValidator::class],
            [
                'type_id',
                'exist',
                'targetClass' => WarehouseType::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', ActiveRecord::STATUS_DELETED]);
                }
            ],

            ['email', 'required'],
            ['email', 'email'],

            ['address', 'required'],
            ['address', 'trim'],
            ['address', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['zip', 'required'],
            ['zip', 'trim'],
            ['zip', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['city', 'required'],
            ['city', 'trim'],
            ['city', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['number', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['group_id', 'integer'],
            [
                'group_id',
                'exist',
                'targetClass' => Group::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', Group::STATUS_DELETED]);
                    $query->andWhere(['type' => GroupType::warehouses->value]);
                },
                'message' => Yii::t('app', 'This group does not exist'),
            ],
            ['group_id', 'default', 'value' => null],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'number' => Yii::t('app', 'Designation number'),
        ];
    }

    public function populate(Warehouse $warehouse): void
    {
        if (!empty($warehouse->warehouseEntity)) {
            $this->entity_id = $warehouse->warehouseEntity->entity_id;
            $this->entity_type = $warehouse->warehouseEntity->entity_type;
        }
    }
}
