<?php

namespace modules\warehouse\providers\query;

use modules\core\providers\query\BaseQueryProvider;
use modules\warehouse\entities\WarehouseZone;
use yii\db\ActiveQuery;

class WarehouseZoneQueryProvider extends BaseQueryProvider
{
    public function getQuery(): ActiveQuery
    {
        return WarehouseZone::find();
    }
}
