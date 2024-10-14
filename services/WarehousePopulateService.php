<?php

namespace modules\warehouse\services;

use frontend\models\search\DashboardSearch;
use modules\warehouse\repositories\WarehouseRepository;
use modules\warehouse\searches\BaseWarehouseSearch;
use Yii;

class WarehousePopulateService
{
    private const WAREHOUSE_STATE_KEY = 'user.dashboard.warehouse';

    public function __construct(
        private readonly WarehouseRepository $warehouseRepository,
    ) {
    }

    public function populateWarehouseSearch(
        BaseWarehouseSearch $warehouseSearch,
        array $data,
        array $extraData = [],
        string|null $formName = 'WarehouseSearch'
    ): bool {
        $dataToLoad = $this->combineFormData($data, $formName);

        if ($extraData) {
            $dataToLoad = array_merge($dataToLoad, $extraData);
        }

        $isLoaded = $warehouseSearch->load($dataToLoad, '');

        if (!empty($warehouseSearch->id)) {
            $warehouseSearch->name = $this->warehouseRepository->getNameById($warehouseSearch->id);
        }

        return $isLoaded;
    }

    private function combineFormData(array $data, ?string $formName): array
    {
        if ($formName !== null && $formName !== '' && array_key_exists($formName, $data)) {
            $formData = $data[$formName];
            unset($data[$formName]);

            return array_merge($data, $formData);
        }

        return $data;
    }

    public function populateDashboardSearch(
        DashboardSearch $dashboardSearchModel,
        bool $isEmptyWarehouseRequest
    ): void {
        $isEmptyState = (! $isEmptyWarehouseRequest || empty($this->getWarehousesState()));

        if (empty($dashboardSearchModel->warehouses) && $isEmptyState) {
            $dashboardSearchModel->warehouses = [];
            $this->setWarehousesState($dashboardSearchModel->warehouses);
        } else {
            $dashboardSearchModel->warehouses = $this->getWarehousesState();
        }
    }

    public function eraseWarehousesState(): void
    {
        $this->setWarehousesState([]);
    }

    public function getWarehousesState(): array
    {
        return Yii::$app->session->get(self::WAREHOUSE_STATE_KEY, []);
    }

    public function setWarehousesState(array $value): void
    {
        Yii::$app->session->set(self::WAREHOUSE_STATE_KEY, $value);
    }
}
