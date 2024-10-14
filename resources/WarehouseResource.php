<?php

namespace modules\warehouse\resources;

use modules\core\resources\components\BaseResource;

class WarehouseResource extends BaseResource
{
    public function __construct(
        public readonly string $id,
        public readonly string $entity_id,
        public readonly string $entity_type,
        public readonly string $type,
        public readonly string $name,
        public readonly string $description,
        public readonly int $status,
        public readonly ?string $address,
        public readonly ?string $zip,
        public readonly ?string $number,
        public readonly ?string $city,
        public readonly ?string $sum_price,
        public readonly ?string $parent_id,
        public readonly ?int $antenna_status,
        public readonly ?int $unique_articles_count,
        public readonly ?array $project
    ) {
    }
}
