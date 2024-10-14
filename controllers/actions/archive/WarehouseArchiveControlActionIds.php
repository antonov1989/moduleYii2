<?php

namespace modules\warehouse\controllers\actions\archive;

enum WarehouseArchiveControlActionIds: string
{
    case createArchive = 'create-archive';
    case restore = 'restore';
}
