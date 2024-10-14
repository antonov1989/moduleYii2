<?php

use frontend\components\web\View;
use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\core\permissions\Permissions;
use siot\general\helpers\Html;
use modules\warehouse\widgets\warehouseZones\WarehouseZonesWidget;

/**
 * @uses WarehouseControlInfoAction
 *
 * @var View $this
 * @var array $zones
 * @var array $zonesMap
 */

$this->title = Yii::t('app', 'Warehouse zones');

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];
$entity = $this->params['entity'];

?>

<?php if (Permissions::getApp()->warehouse()->zoneCreate()->get()): ?>
    <div class="row mb-4">
        <div class="col">
            <?= Html::a(
                Yii::t('app', 'Create new zone'),
                $moduleRouter->to('zone.create', $entity->id),
                ['class' => 'button is-primary', 'icon' => 'plus'],
            ); ?>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($zones)): ?>
    <?= Yii::t('app', 'No zones yet') ?>
<?php else: ?>
    <?= WarehouseZonesWidget::widget([
        'categories' => $zones,
        'addUrl' => function (array $data) use ($moduleRouter, $entity): string {
            return $moduleRouter->to('zone.create', $entity->id, ['parent_id' => $data['id']]);
        },
        'updateUrl' => function (array $data) use ($moduleRouter, $entity): string {
            return $moduleRouter->to('zone.update', $entity->id, ['id' => $data['id']]);
        },
        'deleteUrl' => function (array $data) use ($moduleRouter, $entity): string {
            return $moduleRouter->to('zone.delete', $entity->id, ['id' => $data['id']]);
        },
        'canUpdateZone' => Permissions::getApp()->warehouse()->zoneUpdate()->get(),
        'canDeleteZone' => Permissions::getApp()->warehouse()->zoneDelete()->get(),
        'canCreateZone' => Permissions::getApp()->warehouse()->zoneCreate()->get(),
    ]); ?>
<?php endif; ?>
