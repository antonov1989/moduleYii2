<?php

namespace modules\warehouse\controllers\actions\archive;

use common\components\log\Logger;
use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseArchiveControlController;
use modules\warehouse\services\WarehouseRestoreService;
use Throwable;
use Yii;
use yii\web\Response;

/**
 * @property WarehouseArchiveControlController $controller
 */
class WarehouseArchiveControlRestoreAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseArchiveControlController $controller,
        private readonly WarehouseRestoreService $projectRestoreService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): Response
    {
        try {
            $this->projectRestoreService->restore($this->controller->entity->id);
            $this->handleSuccess();
        } catch (Throwable $exception) {
            $this->handleError($exception);
        }

        return $this->redirectToIndex();
    }

    private function handleSuccess(): void
    {
        $this->alert('success', Yii::t('alert', 'The warehouse have been restored successfully.'));
    }

    private function handleError(Throwable $exception): void
    {
        $this->alert('error', Yii::t('alert', 'Error! The warehouse was not restored.'));
        Logger::log($exception)->error();
    }

    private function redirectToIndex(): Response
    {
        return $this->redirect(
            $this->controller->moduleRouter->to('index')
        );
    }
}
