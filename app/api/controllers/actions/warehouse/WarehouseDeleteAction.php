<?php

namespace modules\warehouse\app\api\controllers\actions\warehouse;

use api\components\base\BaseAction;
use Iwms\Core\General\Components\Response;
use modules\commonTm\api\factories\ApiResourceFactory;
use modules\core\exceptions\ArchiveRestrictionException;
use modules\core\exceptions\EntityNotFoundException;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\SaveException;
use modules\warehouse\app\api\controllers\WarehouseController;
use modules\core\resources\contracts\ApiResourceInterface;
use modules\warehouse\services\WarehouseArchiveService;

class WarehouseDeleteAction extends BaseAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseArchiveService $warehouseArchiveService,
        private readonly ApiResourceFactory $apiResourceFactory
    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @throws ArchiveRestrictionException
     * @throws EntityNotFoundException
     * @throws SaveException
     * @throws EntityValidateException
     */
    public function run(): ApiResourceInterface
    {
        $warehouseId = $this->getRequest()->get('id');

        $this->warehouseArchiveService->archive($warehouseId);

        $this->getResponse()->setStatusCode(Response::STATUS_NO_CONTENT);
        return $this->apiResourceFactory->createNoContentResource();
    }
}
