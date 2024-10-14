<?php

namespace modules\warehouse\factories;

use modules\warehouse\entities\WarehouseEntity;

class WarehouseEntityFactory
{
    public function instantiate(): WarehouseEntity
    {
        return new WarehouseEntity();
    }
}
