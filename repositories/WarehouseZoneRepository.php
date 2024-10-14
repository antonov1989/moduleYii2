<?php

namespace modules\warehouse\repositories;

use modules\core\repositories\BaseRepository;
use modules\warehouse\entities\WarehouseZone;
use modules\warehouse\providers\query\WarehouseZoneQueryProvider;

class WarehouseZoneRepository extends BaseRepository
{
    public function __construct(
        private readonly WarehouseZoneQueryProvider $queryProvider
    ) {
    }

    public function getQueryProvider(): WarehouseZoneQueryProvider
    {
        return $this->queryProvider;
    }

    public function findByWarehouseId(string $id): array
    {
        return $this->getQueryProvider()->getQuery()
            ->select([
                'id',
                'warehouse_id',
                'name',
                'parent_id',
            ])
            ->where(['warehouse_id' => $id])
            ->andWhere(['!=', 'status', WarehouseZone::STATUS_DELETED])
            ->orderBy(['created_at' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
