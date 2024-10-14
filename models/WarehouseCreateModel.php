<?php

namespace modules\warehouse\models;

use Iwms\Core\Group\Entities\Group;
use modules\commonTm\models\BaseModel;
use modules\group\enums\GroupType;
use modules\warehouse\entities\validators\UniqueNamePerEntityTypeValidator;
use modules\warehouse\enums\WarehouseScenario;
use Iwms\Core\WarehouseType\Entities\WarehouseType;
use siot\core\db\ActiveRecord;
use siot\general\settings\Fields;
use siot\general\validators\UuidValidator;
use Yii;
use yii\db\ActiveQuery;

class WarehouseCreateModel extends BaseModel
{
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

    private array $attributeLabels = [
        'number' => 'Designation number',
    ];

    public function rules(): array
    {
        return [
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
            ['address', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['zip', 'required'],
            ['zip', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['city', 'required'],
            ['city', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['number', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['group_id', 'integer'],
            ['group_id', 'default', 'value' => null],
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

            ['entity_id', 'required', 'except' => WarehouseScenario::parentCreationScenario],
        ];
    }

    public function attributeLabels(): array
    {
        return array_map(function($value) {
            return Yii::t('app', $value);
        }, $this->attributeLabels);
    }

    public function addAttributeLabels(array $labels): void
    {
        $this->attributeLabels = array_merge($this->attributeLabels, $labels);
    }
}
