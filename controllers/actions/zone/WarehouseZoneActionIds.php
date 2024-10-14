<?php

namespace modules\warehouse\controllers\actions\zone;

enum WarehouseZoneActionIds: string
{
    case index = 'index';
    case create = 'create';
    case update = 'update';
    case delete = 'delete';
}
