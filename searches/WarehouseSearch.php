<?php

namespace modules\warehouse\searches;

use modules\warehouse\searches\BaseWarehouseSearch;
use siot\core\db\ActiveRecord;

class WarehouseSearch extends BaseWarehouseSearch
{
    public array $status = [ActiveRecord::STATUS_ACTIVE, ActiveRecord::STATUS_INACTIVE];

    public function getModelName(): string
    {
        return 'WarehouseSearch';
    }
}
