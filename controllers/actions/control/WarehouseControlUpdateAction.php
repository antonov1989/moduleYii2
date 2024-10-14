<?php

namespace modules\warehouse\controllers\actions\control;

use common\components\log\Logger;
use modules\core\actions\WebAction;
use modules\core\services\ModelValidateService;
use modules\warehouse\controllers\WarehouseControlController;
use modules\warehouse\models\WarehouseUpdateModel;
use modules\warehouse\services\WarehouseUpdateService;
use Throwable;
use Yii;
use yii\web\Response;

/**
 * @property WarehouseControlController $controller
 */
class WarehouseControlUpdateAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseControlController $controller,
        private readonly WarehouseUpdateModel $warehouseUpdateModel,
        private readonly ModelValidateService $modelValidateService,
        private readonly WarehouseUpdateService $warehouseUpdateService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): Response
    {
        try {
            $this->modelValidateService->validateByForm($this->warehouseUpdateModel, $this->getRequest()->post());
            $this->warehouseUpdateService->update($this->controller->entity->id, $this->warehouseUpdateModel);

            $this->alert('success', Yii::t('app', 'Information was updated successfully.'));
        } catch (Throwable $exception) {
            $this->alert('error', Yii::t('app', 'The warehouse updating has an error.'));

            Logger::log($exception)->error();
        }

        return $this->redirect(
            $this->controller->moduleRouter->to('control.info', $this->controller->entity->id)
        );
    }
}
