<?php

namespace modules\warehouse\controllers\actions\control;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseControlController;
use modules\warehouse\models\WarehouseUpdateModel;
use siot\core\widgets\ActiveForm;
use yii\web\Response;

/**
 * @property WarehouseControlController $controller
 */
class WarehouseControlValidateUpdateAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseControlController $controller,
        private readonly WarehouseUpdateModel $warehouseUpdateModel,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): array
    {
        $this->getResponse()->format = Response::FORMAT_JSON;

        $isLoad = $this->warehouseUpdateModel->load($this->getRequest()->post());

        $this->warehouseUpdateModel->entity_type = $this->controller->warehouseType;

        return $isLoad ? ActiveForm::validate($this->warehouseUpdateModel) : [];
    }
}
