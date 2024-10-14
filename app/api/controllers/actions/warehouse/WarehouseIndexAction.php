<?php

namespace modules\warehouse\app\api\controllers\actions\warehouse;

use api\components\base\BaseAction;
use modules\core\exceptions\ModelValidateException;
use modules\core\resources\contracts\ApiResourceInterface;
use modules\core\services\ModelValidateService;
use modules\warehouse\app\api\controllers\WarehouseController;
use modules\warehouse\app\api\models\WarehouseSearch;
use modules\warehouse\services\WarehouseDataService;

/** @property WarehouseController $controller */
class WarehouseIndexAction extends BaseAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseDataService $warehouseDataService,
        private readonly ModelValidateService $modelValidateService,
        private readonly WarehouseSearch $warehousesSearch,
    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @throws ModelValidateException
     */
    public function run(): ApiResourceInterface
    {
        $params = $this->getRequest()->get();

        $params['entity_type'] = $this->controller->parentEntityType;
        $params['entity_id'] = $this->controller->parentModuleEntityId;

        $this->modelValidateService->validateByAttributes($this->warehousesSearch, $params);

        return $this->warehouseDataService->findWarehouses($this->warehousesSearch);
    }
}
