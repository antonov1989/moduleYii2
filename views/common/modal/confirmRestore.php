<?php

use Iwms\Core\General\Components\Router\ModuleRouter;
use siot\general\helpers\Html;
use siot\core\widgets\ActiveForm;

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];
$entity = $this->params['entity'];

?>

<div id="createConfirmRestore"
     class="modal fade"
     tabindex="-1"
     data-clear-form="#createConfirmRestore"
     aria-labelledby="w0-label"
     style="display: none;"
     aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="w0-label" class="modal-title">
                    <?= Yii::t('app', 'Are you sure?'); ?>
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="<?= Yii::t('app', 'Close'); ?>"
                ></button>
            </div>

            <div class="modal-body">
                <p><?= Yii::t(
                        'app',
                        'Are you sure you want to restore the "{WarehouseName}" warehouse from the archive?',
                        ['WarehouseName' => $entity->name]
                    ); ?></p>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                ><?= Yii::t('app', 'Close'); ?></button>

                <?php $form = ActiveForm::begin([
                    'id' => 'changeRoleModel',
                    'action' => $moduleRouter->to('archive-control.restore', $entity->id),
                    'options' => ['method' => 'post'],
                ]); ?>
                    <?= Html::submitButton(
                        Yii::t('app', 'Restore'),
                        [
                            'class' => 'btn btn-success',
                        ]
                    ); ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
