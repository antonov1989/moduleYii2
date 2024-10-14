<?php

namespace modules\warehouse\controllers\actions\warehouse;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseController;
use modules\warehouse\searches\ArchiveWarehouseSearch;
use modules\warehouse\services\WarehouseDataService;
use modules\warehouse\services\WarehousePopulateService;
use Iwms\Core\WarehouseType\Services\WarehouseTypeSelectService;

class WarehouseArchiveAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly ArchiveWarehouseSearch $archiveWarehouseSearch,
        private readonly WarehouseDataService $warehouseDataService,
        private readonly WarehousePopulateService $warehousePopulateService,
        private readonly WarehouseTypeSelectService $warehouseTypeSelectService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): string
    {
        $this->warehousePopulateService->populateWarehouseSearch(
            $this->archiveWarehouseSearch,
            $this->getRequest()->get(),
            [
                'entity_type' => $this->controller->warehouseType,
            ],
            $this->archiveWarehouseSearch->getModelName(),
        );
        $showSearch = $this->getRequest()->get('filterSubmitted');
        $dataProvider = $this->warehouseDataService->getActiveDataProvider($this->archiveWarehouseSearch);

        $warehouseTypesMap = $this->warehouseTypeSelectService->getMapByModuleType($this->controller->warehouseType);
        $parentEntitiesMap = $this->controller->entityTypeProvider->getListForSelect2();

        return $this->render('archive', [
            'dataProvider' => $dataProvider,
            'archiveWarehouseSearch' => $this->archiveWarehouseSearch,
            'showSearch' => $showSearch,
            'warehouseTypesMap' => $warehouseTypesMap,
            'parentEntitiesMap' => $parentEntitiesMap,
        ]);
    }
}
