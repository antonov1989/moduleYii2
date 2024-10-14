<?php

namespace modules\warehouse\services;

use modules\warehouse\repositories\WarehouseZoneRepository;

class WarehouseZoneService
{
    public function __construct(
        private readonly WarehouseZoneRepository $warehouseRepository
    ) {
    }

    public function findByWarehouseId(string  $id): array
    {
        return $this->warehouseRepository->findByWarehouseId($id);
    }
}
