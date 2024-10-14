<?php

namespace modules\warehouse\searches;

use modules\core\enums\ModuleTypeEnum;
use siot\core\base\Model;
use siot\general\settings\Fields;
use siot\general\validators\UuidValidator;

class WarehouseContractorDependsSearch extends Model
{
    public ?string $name = null;
    public string|array|null $depdrop_all_params = null;
    public array $entity_ids = [];
    public string|ModuleTypeEnum $entity_type;
    public array $statuses = [];
    public array $exclude_ids = [];

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['depdrop_all_params', 'safe'],

            ['entity_ids', 'required'],
            ['entity_ids', 'each', 'rule' => [UuidValidator::class]],

            ['entity_type', 'required'],

            ['statuses', 'each', 'rule' => ['integer']],

            ['exclude_ids', 'each', 'rule' => [UuidValidator::class]],
        ];
    }
}
