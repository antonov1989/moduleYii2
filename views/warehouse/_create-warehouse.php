<?php

use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\models\WarehouseCreateModel;
use siot\core\widgets\ActiveForm;
use siot\general\helpers\Html;
use yii\web\View;

/**
 * @uses modules/warehouse/views/warehouse/index.php
 *
 * @var View $this
 * @var WarehouseCreateModel $model
 * @var array $groupsMap
 * @var array $warehouseTypesMap
 * @var array $parentEntitiesMap
 * @var string $entityType
 *
 * @var string $routeAction
 * @var string $routeValidation
 */

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];

?>

<?php
$form = ActiveForm::begin([
    'id' => 'createWarehouseForm',
    'action' => $moduleRouter->toModule('create', [$entityType, 'warehouse']),
    'enableAjaxValidation' => true,
    'validationUrl' => [$moduleRouter->toModule('validate-create', [$entityType, 'warehouse'])],
]); ?>

<div class="row">
    <div class="col">
        <?= $form->field($model, 'entity_id')
            ->label(Yii::t('app', ucfirst($entityType)))
            ->select2Ajax(
                url: $moduleRouter->to('search.parent-type-ajax-search'),
                options: [
                    'placeholder' => Yii::t('app', 'Choose ' . $entityType),
                    'inputLength' => 0,
                    'pluginOptions' => ['dropdownParent' => '#createWarehouseModal']
                ]
            ); ?>

        <?= $form->field($model, 'type_id')->select2(
            $warehouseTypesMap,
            [
                'placeholder' => Yii::t('app','Choose type'),
                'pluginOptions' => ['dropdownParent' => '#createWarehouseModal'],
            ]
        ); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col">
        <?= $form->field($model, 'name'); ?>
        <?= $form->field($model, 'email'); ?>
        <?= $form->field($model, 'number'); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col">
        <?= $form->field($model, 'address'); ?>
        <?= $form->field($model, 'zip'); ?>
        <?= $form->field($model, 'city'); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col">
        <?= $form->field($model, 'group_id')
            ->select2(
                $groupsMap,
                [
                    'placeholder' => Yii::t('app', 'Select group'),
                    'pluginOptions' => ['dropdownParent' => '#createWarehouseForm'],
                ]
            ); ?>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-md-8 mx-auto mt-3 d-grid">
            <?= Html::submitButton(
                Yii::t('app', 'Create'),
                [
                    'class' => 'button is-primary',
                ]
            ); ?>
        </div>
    </div>
</div>
<?php
ActiveForm::end(); ?>
