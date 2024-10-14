<?php

namespace modules\warehouse\app\api\controllers\actions\warehouse;

enum WarehouseActionIds: string
{
    case index = 'index';
    case create = 'create';
    case update = 'update';
    case delete = 'delete';
    case restore = 'restore';
}
