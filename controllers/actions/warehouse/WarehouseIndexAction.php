<?php

namespace modules\warehouse\controllers\actions\warehouse;

use modules\core\actions\WebAction;
use modules\group\enums\GroupType;
use modules\warehouse\controllers\WarehouseController;
use modules\warehouse\models\WarehouseCreateModel;
use modules\warehouse\searches\WarehouseSearch;
use modules\warehouse\services\WarehousePopulateService;
use modules\warehouse\services\WarehouseDataService;
use Iwms\Core\WarehouseType\Services\WarehouseTypeSelectService;
use modules\group\services\GroupDataService;

class WarehouseIndexAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseSearch $warehouseSearch,
        private readonly WarehouseCreateModel $warehouseCreateModel,
        private readonly WarehouseDataService $warehouseDataService,
        private readonly GroupDataService $groupDataService,
        private readonly WarehousePopulateService $warehousePopulateService,
        private readonly WarehouseTypeSelectService $warehouseTypeSelectService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): string
    {
        $this->warehousePopulateService->populateWarehouseSearch(
            $this->warehouseSearch,
            $this->getRequest()->get(),
            [
                'entity_type' => $this->controller->warehouseType,
            ]
        );
        $showSearch = $this->getRequest()->get('filterSubmitted');
        $dataProvider = $this->warehouseDataService->getActiveDataProvider($this->warehouseSearch);

        $groupsMap = $this->groupDataService->getMapByTypesAndStatus([GroupType::warehouses->value]);
        $warehouseTypesMap = $this->warehouseTypeSelectService->getMapByModuleType($this->controller->warehouseType);
        $parentEntitiesMap = $this->controller->entityTypeProvider->getListForSelect2();

        $this->warehouseCreateModel->addAttributeLabels(['entity_id' => ucfirst($this->controller->warehouseType)]);

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
                'warehouseCreateModel' => $this->warehouseCreateModel,
                'warehouseSearch' => $this->warehouseSearch,
                'showSearch' => $showSearch,
                'groupsMap' => $groupsMap,
                'warehouseTypesMap' => $warehouseTypesMap,
                'parentEntitiesMap' => $parentEntitiesMap,
            ]
        );
    }
}
