<?php

namespace modules\warehouse\searches;

use modules\commonTm\searches\FrontPageSearch;
use siot\general\settings\Fields;
use siot\general\validators\IsArrayValidator;
use siot\general\validators\UuidValidator;
use Yii;

class BaseWarehouseSearch extends FrontPageSearch
{
    public ?string $id = null;
    public ?string $entity_id = null;
    public ?string $entity_type = null;
    public ?string $name = null;
    public ?string $number = null;
    public ?string $address = null;
    public ?string $zip = null;
    public ?string $city = null;
    public ?array $group_ids = [];
    public ?string $search = null;
    public ?string $type_id = null;
    public array $status = [];

    public function getModelName(): string
    {
        return 'BaseWarehouseSearch';
    }

    public function rules(): array
    {
        $rules = [
            ['id', UuidValidator::class],
            ['entity_id', UuidValidator::class],
            ['entity_type', 'required'],
            ['entity_type', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['name', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['number', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['address', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['zip', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['city', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['group_ids', IsArrayValidator::class],
            ['search', 'string'],
            ['status', IsArrayValidator::class],
            ['type_id', UuidValidator::class],
        ];

        return array_merge(parent::rules(), $rules);
    }

    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'Warehouse'),
            'number' => Yii::t('app', 'Designation number'),
            'group_ids' => Yii::t('app', 'Group'),
            'type_id' => Yii::t('app', 'Type'),
        ];
    }

    public function attributeHints(): array
    {
        return [
            'id' => Yii::t('app', 'Search by \'Name\''),
        ];
    }
}
