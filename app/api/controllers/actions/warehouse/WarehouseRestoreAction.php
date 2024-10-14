<?php

namespace modules\warehouse\app\api\controllers\actions\warehouse;

use api\components\base\BaseAction;
use modules\core\exceptions\EntityNotFoundException;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\RestoreException;
use modules\core\exceptions\SaveException;
use modules\warehouse\app\api\controllers\WarehouseController;
use modules\core\resources\contracts\ApiResourceInterface;
use modules\warehouse\factories\WarehouseResourceFactory;
use modules\warehouse\services\WarehouseRestoreService;

class WarehouseRestoreAction extends BaseAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseRestoreService $warehouseArchiveService,
        private readonly WarehouseResourceFactory $warehouseResourceFactory,
    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @throws EntityNotFoundException
     * @throws SaveException
     * @throws RestoreException
     * @throws EntityValidateException
     */
    public function run(): ApiResourceInterface
    {
        $warehouseId = $this->getRequest()->get('id');

        return $this->warehouseResourceFactory->createWarehouseResource(
            $this->warehouseArchiveService->restore($warehouseId)
        );
    }
}
