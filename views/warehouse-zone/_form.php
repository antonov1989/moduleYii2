<?php

use frontend\helpers\Url;
use modules\warehouse\entities\WarehouseZone;
use kartik\depdrop\DepDrop;
use siot\core\widgets\ActiveForm;
use siot\general\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var WarehouseZone $model
 * @var array $zonesMap
 */

?>

<?php $form = ActiveForm::begin([
    'id' => 'groupsForm',
    'options' => ['method' => 'post'],
]); ?>
<div class="row">
    <div class="col-6">
        <?= $form->field($model, 'name'); ?>
    </div>
    <div class="col-6">
        <?= $form->field($model, 'parent_id')->widget(DepDrop::class, [
            'type' => DepDrop::TYPE_SELECT2,
            'options' => ['id' => 'parent-id', 'placeholder' => 'Select...'],
            'data' => $zonesMap,
            'select2Options' => ['pluginOptions' => ['allowClear' => true]],
            'pluginOptions' => [
                'initialize' => true,
                'depends' => [Html::getInputId($model, 'warehouse_id')],
                'placeholder' => Yii::t('app', 'Select...'),
                'url' => Url::getEntityUrl([
                    '/get-by-warehouse',
                    'group_id' => $model->isNewRecord ? null : $model->id,
                    'group_path' => $model->isNewRecord ? null : $model->parent_path
                ]),
            ],
        ]); ?>
    </div>
</div>

<div class="row">
    <div class="col text-end">
        <?= Html::submitButton(
            'Submit',
            [
                'class' => 'button is-primary',
                'icon' => 'save',
            ]
        ); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
