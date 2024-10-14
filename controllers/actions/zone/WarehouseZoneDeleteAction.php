<?php

namespace modules\warehouse\controllers\actions\zone;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseZoneController;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\entities\WarehouseZone;
use modules\warehouse\services\WarehouseZoneService;
use Yii;
use yii\db\Expression;
use yii\web\Response;

/**
 * @property WarehouseZoneController $controller
 */
class WarehouseZoneDeleteAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseZoneController $controller,
        private readonly WarehouseZoneService $warehouseZoneService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): Response
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->controller->entity;

        $warehouseZoneId = $this->getRequest()->get('id');
        $model = WarehouseZone::findOne($warehouseZoneId);

        $zonesIds = WarehouseZone::find()
            ->select(['id'])
            ->where(['<@', 'parent_path', new Expression("'$model->parent_path'")])
            ->column();
        $deletedRows = WarehouseZone::getDb()
            ->createCommand()
            ->update(
                WarehouseZone::tableName(),
                [
                    'status' => WarehouseZone::STATUS_DELETED,
                    'deleted_at' => time()
                ],
                ['id' => $zonesIds]
            )
            ->execute();

        if ($deletedRows > 0) {
            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'Zone deleted successfully'));
        }

        return $this->redirect(
            $this->controller->moduleRouter->to('zone.index', $warehouse->id),
        );
    }
}
