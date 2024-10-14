<?php

namespace modules\warehouse\app\api\models;

use modules\core\searches\PageSearch;
use modules\warehouse\entities\Warehouse;
use siot\general\settings\Fields;
use siot\general\validators\IsArrayValidator;
use siot\general\validators\UuidValidator;

class WarehouseSearch extends PageSearch
{
    public ?string $id = null;
    public ?string $entity_id = null;
    public ?string $entity_type = null;
    public ?int $type = null;
    public array $status = [Warehouse::STATUS_ACTIVE];
    public ?string $name = null;

    public function __construct(
        private readonly Warehouse $warehouse
    ) {
        parent::__construct();
    }

    public function rules(): array
    {
        $rules = [
            ['id', UuidValidator::class],

            ['entity_id', UuidValidator::class],

            ['entity_type', 'string', 'max' => Fields::VARCHAR_LENGTH],

            ['type', 'integer'],

            ['status', IsArrayValidator::class],
            ['status', 'each', 'rule' => ['in', 'range' => $this->warehouse->getStatuses()]],

            ['name', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['name', 'trim'],
        ];

        return array_merge(parent::rules(), $rules);
    }
}
