<?php

namespace modules\warehouse\services;

use Iwms\Core\General\Providers\Data\ModuleEntityProviderInterface;
use modules\core\factories\ResourceFactory;
use modules\core\providers\data\DataProviderFactory;
use modules\core\resources\collection\ResourceCollection;
use modules\warehouse\app\api\models\WarehouseSearch;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\factories\WarehouseResourceFactory;
use modules\warehouse\providers\query\WarehouseQueryProvider;
use modules\warehouse\repositories\WarehouseRepository;
use modules\warehouse\searches\WarehouseCompanyDependsSearch;
use modules\warehouse\searches\BaseWarehouseSearch;
use modules\warehouse\searches\WarehouseContractorDependsSearch;
use yii\data\ActiveDataProvider;

class WarehouseDataService implements ModuleEntityProviderInterface
{
    public function __construct(
        private readonly WarehouseQueryProvider $warehouseQueryProvider,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly DataProviderFactory $dataProviderFactory,
        private readonly WarehouseResourceFactory $warehouseResourceFactory,
        private readonly ResourceFactory $resourceFactory
    ) {
    }

    public function getEntityById(int|string $id): Warehouse|null
    {
        return $this->warehouseRepository->findById($id);
    }

    public function getActiveDataProvider(BaseWarehouseSearch $warehouseSearch): ActiveDataProvider
    {
        $query = $this->warehouseQueryProvider->getQueryBySearchModel($warehouseSearch);

        $dataProvider =  $this->dataProviderFactory->createActiveDataProviderByQuery($query);
        $dataProvider->pagination->pageSize = $warehouseSearch->pageSize;

        return $dataProvider;
    }

    public function getWarehouseById(string $id): Warehouse|null
    {
        return $this->warehouseRepository->findById($id);
    }

    public function getWarehouseNameById(string $id): string|null
    {
        return $this->warehouseRepository->getNameById($id);
    }

    public function isExistActiveWarehouse(WarehouseContractorDependsSearch $search): bool
    {
        return $this->warehouseRepository->isExistActiveWarehouse($search);
    }

    public function getActiveWarehouseQuery(WarehouseContractorDependsSearch $search): ActiveDataProvider
    {
        $query = $this->warehouseQueryProvider->getActiveWarehouseQuery($search);

        return $this->dataProviderFactory
            ->setSort(['attributes' => ['name']])
            ->createActiveDataProviderByQuery($query);
    }

    public function findWarehousesByUser($userId): array
    {
        return $this->warehouseRepository->findAllActiveWarehousesByUser($userId);
    }

    public function findAllActiveWarehouses(): array
    {
        return $this->warehouseRepository->findAllActiveWarehouses();
    }

    public function getMapForDepDrop(WarehouseContractorDependsSearch $search): array
    {
        return [
            'output' => array_values(
                $this->warehouseRepository->getMapForDepDrop($search)
            ),
            'selected' => '',
        ];
    }

    public function getIdsOfActive(WarehouseContractorDependsSearch $warehouseSearch): array
    {
        return $this->warehouseRepository->getIdsOfActive($warehouseSearch);
    }

    public function getAllActiveWarehouses(): array
    {
        return $this->warehouseRepository->getAllActiveWarehouses();
    }

    public function findWarehouses(WarehouseSearch $warehousesSearch): ResourceCollection
    {
        $offset = $warehousesSearch->getOffset();
        $limit = $warehousesSearch->getLimit();

        $warehouses = $this->warehouseRepository->findAllBySearchModel($warehousesSearch, $offset, $limit);
        $totalCount = $this->warehouseRepository->findAllBySearchModelCount($warehousesSearch);

        $meta = $this->resourceFactory->createMeta(
            totalCount: $totalCount,
            pageSize: $limit,
            page: $warehousesSearch->getCurrentPage(),
        );

        return $this->warehouseResourceFactory->createWarehouseResourceCollection($warehouses, $meta);
    }

    /**
     * TODO: remove after will merge new core warehouses
     */
    public function getMapForDepDropByCompaniesIds(WarehouseCompanyDependsSearch $search): array
    {
        return [
            'output' => array_values(
                $this->warehouseRepository->getMapForDepDropByCompaniesIds(
                    $search->companies,
                    $search->name
                )
            ),
            'selected' => '',
        ];
    }

}
