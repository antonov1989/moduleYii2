<?php

namespace modules\warehouse\controllers\actions\warehouse;

use common\components\log\Logger;
use Exception;
use modules\commonTm\helpers\params\ParameterNotFoundException;
use modules\core\actions\WebAction;
use modules\core\services\ModelValidateService;
use modules\warehouse\controllers\WarehouseController;
use modules\warehouse\models\WarehouseCreateModel;
use modules\warehouse\services\WarehouseCreateService;
use Yii;
use yii\web\Response;

class WarehouseCreateAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly ModelValidateService $modelValidateService,
        private readonly WarehouseCreateService $warehouseCreateService,
        private readonly WarehouseCreateModel $warehouseCreateModel,
    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @return Response
     * @throws ParameterNotFoundException
     */
    public function run(): Response
    {
        try {
            $this->warehouseCreateModel->entity_type = $this->controller->warehouseType;
            $this->modelValidateService->validateByForm($this->warehouseCreateModel, $this->getRequest()->post());
            $this->warehouseCreateService->create($this->warehouseCreateModel);

            $this->alert(
                'success',
                Yii::t('app', 'The new warehouse has been successfully created.')
            );
        } catch (Exception $exception) {
            $this->alert('error', Yii::t('app', 'Warehouse creation error.'));

            Logger::log($exception)->error();
        }

        return $this->redirect(
            $this->controller->moduleRouter->to('index')
        );
    }
}
