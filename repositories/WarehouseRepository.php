<?php

namespace modules\warehouse\repositories;

use Iwms\Core\General\Providers\Data\ModuleEntityProviderInterface;
use modules\core\enums\SearchLimitEnum;
use modules\core\repositories\BaseRepository;
use modules\warehouse\app\api\models\WarehouseSearch as ApiWarehouseSearch;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\providers\query\WarehouseQueryProvider;
use modules\warehouse\searches\WarehouseContractorDependsSearch;
use modules\warehouse\searches\WarehouseSearch;

class WarehouseRepository extends BaseRepository implements ModuleEntityProviderInterface
{
    public function __construct(
        private readonly WarehouseQueryProvider $warehouseQueryProvider
    ) {
    }

    public function getQueryProvider(): WarehouseQueryProvider
    {
        return $this->warehouseQueryProvider;
    }

    public function getEntityById(string|int $id): Warehouse|null
    {
        /** @var Warehouse|null */
        return $this->findById($id);
    }

    public function findById(string|int $id): Warehouse|null
    {
        /** @var Warehouse|null  */
        return $this->getQueryProvider()->getQuery()
            ->andWhere(['id' => $id])
            ->one();
    }

    public function getNameById(string|null $warehouseId): string|null
    {
        $query = $this->getQueryProvider()->getQuery();
        $query->select(['name']);
        $query->where(['id' => $warehouseId]);

        return $query->scalar();
    }

    public function isExistActiveWarehouse(WarehouseContractorDependsSearch $search): bool
    {
        $query = $this->getQueryProvider()->getActiveWarehouseQuery($search);

        return $query->exists();
    }

    public function findAllActiveWarehousesByUser($userId): array
    {
        return  $this->getQueryProvider()->getActiveQuery()
            ->joinWith('employees', false, 'INNER JOIN')
            ->where(['user_id' => $userId])->all();
    }

    public function findAllActiveWarehouses(): array
    {
        return  $this->getQueryProvider()->getActiveQuery()->all();
    }

    public function getMapForDepDrop(WarehouseContractorDependsSearch $search): array
    {
        return $this->getQueryProvider()->getQuery()
            ->select([
                'warehouse.id',
                'name'
            ])
            ->joinWith('warehouseEntity', false, 'INNER JOIN')
            ->where(['warehouse_entity.entity_id' => $search->entity_ids])
            ->andWhere(['warehouse_entity.entity_type' => $search->entity_type])
            ->andFilterWhere(['ILIKE', 'name', $search->name])
            ->andFilterWhere(['status' => $search->statuses])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    public function getIdsOfActive(WarehouseContractorDependsSearch $warehouseSearch): array
    {
        return $this->getQueryProvider()
            ->getActiveWarehouseQuery($warehouseSearch)
            ->select(['warehouse.id'])
            ->column();
    }

    public function findAllBySearchModel(ApiWarehouseSearch $warehousesSearch, int $offset, int $limit): array
    {
        $query = $this->warehouseQueryProvider->getWarehouseSearchQuery($warehousesSearch);

        $query->offset($offset);
        $query->limit($limit);

        return $query->all();
    }

    public function findAllBySearchModelCount(ApiWarehouseSearch $warehousesSearch): int
    {
        return $this->warehouseQueryProvider->getWarehouseSearchQuery($warehousesSearch)->count();
    }

    public function getAllActiveWarehouses(): array
    {
        return $this->getQueryProvider()->getActiveQuery()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    public function getForSelect2Ajax(WarehouseSearch $warehousesSearch): array
    {
        $query = $this->getQueryProvider()->getQueryBySearchModel($warehousesSearch);

        return $query
            ->select([
                 'warehouse.id',
                 'text' => 'name',
             ])
            ->andFilterWhere(['ILIKE', 'name', $warehousesSearch->search])
            ->orderBy('name')
            ->limit(SearchLimitEnum::smallLimit->value)
            ->asArray()
            ->all();
    }

    /**
     * TODO: remove after will merge new core warehouses
     */
    public function getMapForDepDropByCompaniesIds(array $companiesIds, string|null $name = null): array
    {
        return $this->getQueryProvider()->getQuery()
            ->select([
                 'id',
                 'name'
             ])
            ->where(['company_id' => $companiesIds])
            ->andFilterWhere(['ILIKE', 'name', $name])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
