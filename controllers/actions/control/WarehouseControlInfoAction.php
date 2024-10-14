<?php

namespace modules\warehouse\controllers\actions\control;

use modules\core\actions\WebAction;
use modules\group\enums\GroupType;
use modules\group\services\GroupDataService;
use modules\warehouse\models\WarehouseUpdateModel;
use modules\warehouse\controllers\WarehouseControlController;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\services\WarehouseArchiveCheckRestrictionsService;
use Iwms\Core\WarehouseType\Services\WarehouseTypeSelectService;

/**
 * @property WarehouseControlController $controller
 */
class WarehouseControlInfoAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseControlController $controller,
        private readonly WarehouseUpdateModel $warehouseUpdateModel,
        private readonly GroupDataService $groupDataService,
        private readonly WarehouseTypeSelectService $warehouseTypeSelectService,
        private readonly WarehouseArchiveCheckRestrictionsService $warehouseArchiveCheckRestrictionsService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): string
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->controller->entity;

        $this->warehouseUpdateModel->entity_type = $this->controller->warehouseType;
        if ($this->warehouseUpdateModel->load($warehouse->toArray(), '')) {
            $this->warehouseUpdateModel->populate($warehouse);
        }

        $groupsMap = $this->groupDataService->getMapByTypesAndStatus([GroupType::warehouses->value]);
        $warehouseTypesMap = $this->warehouseTypeSelectService->getMapByModuleType($this->controller->warehouseType);
        $parentEntitiesMap = $this->controller->entityTypeProvider->getListForSelect2();

        return $this->render('info', [
            'warehouseUpdateModel' => $this->warehouseUpdateModel,
            'groupsMap' => $groupsMap,
            'warehouseTypesMap' => $warehouseTypesMap,
            'parentEntitiesMap' => $parentEntitiesMap,
            'isAllowedToBeArchived' => $this->warehouseArchiveCheckRestrictionsService->isAllowedToBeArchived($warehouse->id),
        ]);
    }
}
