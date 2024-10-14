<?php

namespace modules\warehouse\controllers\actions\control;

enum WarehouseControlActionIds: string
{
    case info = 'info';
    case update = 'update';
    case validateUpdate = 'validate-update';
}
