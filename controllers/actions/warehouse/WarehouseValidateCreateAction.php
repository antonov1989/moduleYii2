<?php

namespace modules\warehouse\controllers\actions\warehouse;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseController;
use modules\warehouse\models\WarehouseCreateModel;
use siot\core\widgets\ActiveForm;
use yii\web\Response;

class WarehouseValidateCreateAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseController $controller,
        private readonly WarehouseCreateModel $warehouseCreateModel
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): array
    {
        $this->getResponse()->format = Response::FORMAT_JSON;

        $isLoad = $this->warehouseCreateModel->load($this->getRequest()->post());

        $this->warehouseCreateModel->entity_type = $this->controller->warehouseType;
        $this->warehouseCreateModel->addAttributeLabels(['entity_id' => ucfirst($this->controller->warehouseType)]);

        return $isLoad ? ActiveForm::validate($this->warehouseCreateModel) : [];
    }
}
