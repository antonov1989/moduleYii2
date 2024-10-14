<?php

use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\warehouse\searches\BaseWarehouseSearch;
use siot\core\widgets\ActiveForm;
use siot\general\helpers\Html;
use siot\general\helpers\Url;
use siot\general\widgets\Card;
use yii\web\View;

/**
 * @uses modules/warehouse/views/warehouse/index.php
 * @uses modules/warehouse/views/warehouse/archive.php
 *
 * @var View $this
 * @var BaseWarehouseSearch $searchModel
 * @var bool $showSearch
 * @var string $routeWarehouseAjaxSearch
 * @var array $parentEntitiesMap
 * @var array $warehouseTypesMap
 */

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];

?>

<?php
Card::begin(); ?>
<?php
$form = ActiveForm::begin([
    'method' => 'GET',
    'id' => 'warehouseSearchForm',
    'action' => Url::remove([$searchModel->getModelName()]),
    'options' => [
        'x-data' => '{ loading: false }',
        '@submit' => 'loading = true',
    ],
]); ?>
<div class="row">
    <div class="col-6">
        <?= $form->field($searchModel, 'id')->select2Ajax(
            [
                $routeWarehouseAjaxSearch,
                'status' => $searchModel->status,
            ],
            $searchModel->id !== null
                ? [$searchModel->id => $searchModel->name]
                : [],
            [
                'placeholder' => Yii::t('app', 'Choose warehouse'),
                'inputLength' => 0,
                'onchange' => 'this.form.submit()',
            ]
        ); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($searchModel, 'number'); ?>
        <?= $form->field($searchModel, 'address'); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($searchModel, 'zip'); ?>
        <?= $form->field($searchModel, 'city'); ?>
    </div>

    <div class="col-md-4">
        <?= $form->field($searchModel, 'entity_id')
            ->label(Yii::t('app', ucfirst($searchModel->entity_type)))
            ->select2Ajax(
            [
                $moduleRouter->to('search.parent-type-ajax-search'),
            ],
            !empty($searchModel->entity_id) && isset($parentEntitiesMap[$searchModel->entity_id])
                ? [$searchModel->entity_id => $parentEntitiesMap[$searchModel->entity_id]]
                : [],
            [
                'placeholder' => Yii::t('app', 'Choose ' . $searchModel->entity_type),
                'inputLength' => 0,
                'onchange' => 'this.form.submit()',
                'pluginOptions' => ['dropdownParent' => '#warehouseSearchForm']
            ]
        ); ?>

        <?= $form->field($searchModel, 'type_id')->select2(
            $warehouseTypesMap,
            [
                'placeholder' => Yii::t('app','Choose type'),
                'onchange' => 'this.form.submit()',
                'pluginOptions' => ['dropdownParent' => '#warehouseSearchForm'],
            ]
        ); ?>
    </div>
    <?= \yii\helpers\Html::hiddenInput('filterSubmitted', 'true') ?>
</div>

<div class="row">
    <div class="col-md-12 text-end">

        <?php
        if ($showSearch): ?>
            <?= Html::a(
                Yii::t('app', 'Reset filter'),
                Url::remove([$searchModel->getModelName(), 'filterSubmitted']),
                ['class' => 'button is-danger is-small', 'icon' => 'times']
            ); ?>
        <?php
        endif; ?>
        <?= Html::submitButton(
            Yii::t('app', 'Search'),
            ['class' => 'button is-success', 'icon' => 'search']
        ); ?>
    </div>
</div>
<?php
ActiveForm::end(); ?>
<?php
Card::end(); ?>
