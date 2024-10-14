<?php

namespace modules\warehouse\searches;

use modules\warehouse\searches\BaseWarehouseSearch;
use siot\core\db\ActiveRecord;

class ArchiveWarehouseSearch extends BaseWarehouseSearch
{
    public array $status = [ActiveRecord::STATUS_DELETED];

    public function getModelName(): string
    {
        return 'ArchiveWarehouseSearch';
    }
}
