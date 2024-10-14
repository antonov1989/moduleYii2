<?php

namespace modules\warehouse\app\api\controllers\actions\warehouse;

use api\components\base\BaseAction;
use Iwms\Core\General\Components\Response;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\ModelValidateException;
use modules\core\exceptions\SaveException;
use modules\core\resources\contracts\ApiResourceInterface;
use modules\core\services\ModelValidateService;
use modules\warehouse\app\api\controllers\WarehouseController;
use modules\warehouse\factories\WarehouseResourceFactory;
use modules\warehouse\models\WarehouseCreateModel;
use modules\warehouse\services\WarehouseCreateService;

/**
 * @property WarehouseController $controller
 */
class WarehouseCreateAction extends BaseAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseCreateModel $warehouseCreateModel,
        private readonly ModelValidateService $modelValidateService,
        private readonly WarehouseCreateService $warehouseCreateService,
        private readonly WarehouseResourceFactory $warehouseResourceFactory,

    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @throws SaveException
     * @throws EntityValidateException
     * @throws ModelValidateException
     */
    public function run(): ApiResourceInterface
    {
        $this->warehouseCreateModel->entity_type = $this->controller->parentEntityType;
        $this->warehouseCreateModel->entity_id = $this->controller->parentModuleEntityId;

        $this->modelValidateService->validateByAttributes(
            $this->warehouseCreateModel,
            $this->getRequest()->post()
        );

        $warehouse = $this->warehouseCreateService->create($this->warehouseCreateModel);

        $this->getResponse()->setStatusCode(Response::STATUS_CREATED);

        return $this->warehouseResourceFactory->createWarehouseResource($warehouse);
    }
}

