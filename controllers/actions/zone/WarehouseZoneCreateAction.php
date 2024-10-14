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
class WarehouseZoneCreateAction extends WebAction
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
        $model = new WarehouseZone(['warehouse_id' => $warehouse->id]);

        if ($model->load($this->getRequest()->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('alert', 'New zone created successfully'));

            return $this->redirect(
                $this->controller->moduleRouter->to('zone.index', $warehouse->id),
            );
        }

        $model->parent_id = $this->getRequest()->get('parent_id');

        $zones = $this->warehouseZoneService->findByWarehouseId($warehouse->id);
        $zonesMap = ArrayHelper::map($zones, 'id', 'name');

        return $this->render('create', [
            'model' => $model,
            'zonesMap' => $zonesMap,
        ]);
    }
}
