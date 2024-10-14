<?php

namespace modules\warehouse\controllers\actions\archive;

use common\components\log\Logger;
use modules\commonTm\helpers\params\ParameterNotFoundException;
use modules\core\actions\WebAction;
use modules\core\exceptions\ArchiveRestrictionException;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\SaveException;
use modules\warehouse\controllers\WarehouseArchiveControlController;
use modules\warehouse\services\WarehouseArchiveService;
use Throwable;
use Yii;
use yii\web\Response;

/**
 * @property WarehouseArchiveControlController $controller
 */
class WarehouseArchiveControlCreateArchiveAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseArchiveControlController $controller,
        private readonly WarehouseArchiveService $warehouseArchiveService,
    ) {
        parent::__construct($id, $controller);
    }

    /**
     * @throws ParameterNotFoundException
     */
    public function run(): Response
    {
        try {
            $this->archive();
            $this->handleSuccess();
        } catch (ArchiveRestrictionException) {
            return $this->redirectArchiveRestriction();
        } catch (Throwable $exception) {
            $this->handleArchiveError($exception);
        }

        return $this->redirectToIndex();
    }

    /**
     * @throws EntityValidateException
     * @throws ArchiveRestrictionException
     * @throws SaveException
     */
    private function archive(): void
    {
        $this->warehouseArchiveService->archive($this->controller->entity->id);
    }

    private function handleSuccess(): void
    {
        $this->alert('success', Yii::t('alert', 'The warehouse has been archived successfully.'));
    }

    private function redirectArchiveRestriction(): Response
    {
        return $this->redirectToIndex();

        // TODO: need add restrictions
        return $this->redirect(
            $this->controller->moduleRouter->to('archive-control.restrictions', $this->controller->entity->id)
        );
    }

    private function handleArchiveError(Throwable $exception): void
    {
        $this->alert('error', Yii::t('alert', 'Error! The warehouse was not archived'));
        Logger::log($exception)->error();
    }

    /**
     * @throws ParameterNotFoundException
     */
    private function redirectToIndex(): Response
    {
        return $this->redirect(
            $this->controller->moduleRouter->to('index')
        );
    }
}
