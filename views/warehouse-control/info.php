<?php

use frontend\components\web\View;
use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\warehouse\models\WarehouseUpdateModel;
use siot\core\widgets\ActiveForm;
use siot\general\helpers\Html;

/**
 * @uses WarehouseControlInfoAction
 *
 * @var View $this
 * @var WarehouseUpdateModel $warehouseUpdateModel
 * @var array $groupsMap
 * @var array $warehouseTypesMap
 * @var array $parentEntitiesMap
 * @var bool $isAllowedToBeArchived
 */

$this->params['isAllowedToBeArchived'] = $isAllowedToBeArchived;

$this->title = Yii::t('app', 'Information');

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];
$entity = $this->params['entity'];

$entityType = $warehouseUpdateModel->entity_type;

?>

<?php
$form = ActiveForm::begin([
    'id' => 'updateWarehouseForm',
    'action' => $moduleRouter->to('control.update', $entity->id),
    'enableAjaxValidation' => true,
    'validationUrl' => [$moduleRouter->to('control.validate-update')],
]); ?>
<div class="row">
    <div class="col">
        <?= $form->field($warehouseUpdateModel, 'id')
            ->hiddenInput(['value' => $warehouseUpdateModel->id])
            ->label(false);
        ?>
        <?= $form->field($warehouseUpdateModel, 'entity_id')
            ->label(Yii::t('app', ucfirst($entityType)))
            ->select2(
                $parentEntitiesMap,
                [
                    'placeholder' => Yii::t('app','Choose ' . $entityType),
                    'disabled' => true,
                ]
            ); ?>

        <?= $form->field($warehouseUpdateModel, 'type_id')->select2(
            $warehouseTypesMap,
            [
                'placeholder' => Yii::t('app','Choose warehouse type'),
            ]
        ); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col">
        <?= $form->field($warehouseUpdateModel, 'name'); ?>
        <?= $form->field($warehouseUpdateModel, 'email'); ?>
        <?= $form->field($warehouseUpdateModel, 'number'); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col">
        <?= $form->field($warehouseUpdateModel, 'address'); ?>
        <?= $form->field($warehouseUpdateModel, 'zip'); ?>
        <?= $form->field($warehouseUpdateModel, 'city'); ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col">
        <?= $form->field($warehouseUpdateModel, 'group_id')
            ->select2(
                $groupsMap,
                [
                    'placeholder' => Yii::t('app', 'Select group'),
                ]
            );
        ?>
        <div class="form-group">
            <div class="row">
                <div class="col-md-8 mx-auto mt-3 d-grid">
                    <?= Html::submitButton(
                        Yii::t('app', 'Save'),
                        [
                            'class' => 'button is-primary',
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
ActiveForm::end(); ?>
