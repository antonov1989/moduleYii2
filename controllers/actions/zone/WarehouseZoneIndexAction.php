<?php

namespace modules\warehouse\controllers\actions\zone;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseZoneController;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\services\WarehouseZoneService;
use siot\general\helpers\ArrayHelper;

/**
 * @property WarehouseZoneController $controller
 */
class WarehouseZoneIndexAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseZoneController $controller,
        private readonly WarehouseZoneService $warehouseZoneService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): string
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->controller->entity;

        $zones = $this->warehouseZoneService->findByWarehouseId($warehouse->id);

        $zonesMap = ArrayHelper::map($zones, 'id', 'name');
        $zonesTree = ArrayHelper::buildTree($zones);

        return $this->render('index', [
            'zones' => $zonesTree,
            'zonesMap' => $zonesMap
        ]);
    }
}
