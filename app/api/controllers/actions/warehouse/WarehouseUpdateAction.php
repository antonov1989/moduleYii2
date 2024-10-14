<?php

namespace modules\warehouse\app\api\controllers\actions\warehouse;

use api\components\base\BaseAction;
use modules\core\exceptions\ModelValidateException;
use modules\core\resources\contracts\ApiResourceInterface;
use modules\core\services\ModelValidateService;
use modules\warehouse\app\api\controllers\WarehouseController;
use modules\warehouse\factories\WarehouseResourceFactory;
use modules\warehouse\models\WarehouseUpdateModel;
use modules\warehouse\services\WarehouseUpdateService;

/**
 * @property WarehouseController $controller
 */
class WarehouseUpdateAction extends BaseAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseUpdateModel $warehouseUpdateModel,
        private readonly ModelValidateService $modelValidateService,
        private readonly WarehouseUpdateService $warehouseUpdateService,
        private readonly WarehouseResourceFactory $warehouseResourceFactory
    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @throws ModelValidateException
     */
    public function run(): ApiResourceInterface
    {
        $this->modelValidateService->validateByAttributes(
            $this->warehouseUpdateModel,
            $this->getRequest()->post(),
        );

        $warehouseId = $this->getRequest()->get('id');

        return $this->warehouseResourceFactory->createWarehouseResource(
            $this->warehouseUpdateService->update($warehouseId, $this->warehouseUpdateModel)
        );
    }
}
