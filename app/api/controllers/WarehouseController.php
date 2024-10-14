<?php

namespace modules\warehouse\app\api\controllers;

use modules\commonTm\api\controllers\EntityRestController;
use modules\warehouse\app\api\controllers\actions\warehouse\WarehouseActionIds;
use modules\warehouse\app\api\controllers\actions\warehouse\WarehouseCreateAction;
use modules\warehouse\app\api\controllers\actions\warehouse\WarehouseIndexAction;
use modules\warehouse\app\api\controllers\actions\warehouse\WarehouseDeleteAction;
use modules\warehouse\app\api\controllers\actions\warehouse\WarehouseRestoreAction;
use modules\warehouse\app\api\controllers\actions\warehouse\WarehouseUpdateAction;

class WarehouseController extends EntityRestController
{
    public function actions(): array
    {
        return [
            WarehouseActionIds::index->value => WarehouseIndexAction::class,
            WarehouseActionIds::create->value => WarehouseCreateAction::class,
            WarehouseActionIds::update->value => WarehouseUpdateAction::class,
            WarehouseActionIds::delete->value => WarehouseDeleteAction::class,
            WarehouseActionIds::restore->value => WarehouseRestoreAction::class,
        ];
    }
}
