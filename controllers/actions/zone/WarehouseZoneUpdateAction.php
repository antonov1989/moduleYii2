<?php

namespace modules\warehouse\controllers\actions\zone;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseZoneController;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\entities\WarehouseZone;
use modules\warehouse\services\WarehouseZoneService;
use siot\general\helpers\ArrayHelper;
use Yii;
use yii\web\Response;

/**
 * @property WarehouseZoneController $controller
 */
class WarehouseZoneUpdateAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseZoneController $controller,
        private readonly WarehouseZoneService $warehouseZoneService,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): string|Response
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->controller->entity;

        $warehouseZoneId = $this->getRequest()->get('id');
        $model = WarehouseZone::findOne($warehouseZoneId);

        if ($model->load(Yii::$app->getRequest()->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'Zone updated successfully'));

                return $this->redirect(
                    $this->controller->moduleRouter->to('zone.index', $warehouse->id),
                );
            }
        }

        $zones = $this->warehouseZoneService->findByWarehouseId($warehouse->id);
        $zonesMap = ArrayHelper::map($zones, 'id', 'name');
        unset($zonesMap[$model->id]);

        return $this->render('update', [
            'model' => $model,
            'zonesMap' => $zonesMap,
        ]);
    }
}
