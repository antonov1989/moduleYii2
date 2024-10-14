<?php

use Iwms\Core\Reason\Entities\Reason;
use yii\web\View;
use common\models\{
    Article,
    search\WarehouseSearch
};
use frontend\helpers\Url;
use siot\general\helpers\Html;
use rmrevin\yii\fontawesome\FAS;
use siot\core\widgets\ActiveForm;
use modules\warehouse\searches\WarehouseTransactionSearch;

/**
 * @var View $this
 * @var WarehouseTransactionSearch $model
 */

?>

<hr>

<?php $form = ActiveForm::begin([
    'id' => 'transactionSearchForm',
    'action' => Url::remove('TransactionsSearch'),
    'method' => 'get'
]); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'type')->select2($model->getTypesMap(), [
                'placeholder' => 'Choose type',
                'onchange' => 'this.form.submit()',
                'pluginOptions' => ['dropdownParent' => '#transactionSearchForm']
            ]); ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'unique_article_serial')->select2Ajax(
                ['/unique-articles/ajax-search-by-serial'],
                [],
                [
                    'placeholder' => Yii::t('app', 'Serial number'),
                    'inputLength' => 0,
                    'onchange' => 'this.form.submit()'
                ]
            ); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'unique_article_code')->select2Ajax(
                ['/unique-articles/ajax-search-by-code'],
                [],
                [
                    'placeholder' => Yii::t('app', 'Code'),
                    'inputLength' => 0,
                    'onchange' => 'this.form.submit()'
                ]
            ); ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'article_id')->select2Ajax(
                ['/articles/ajax-search-articles'],
                $model->article_id ? [$model->article_id => Article::getNameById($model->article_id, true)] : [],
                [
                    'placeholder' => 'Choose product',
                    'inputLength' => 0,
                    'onchange' => 'this.form.submit()'
                ]
            ); ?>
            <?= $form->field($model, 'warehouse_id')->select2Ajax(
                ['/warehouses/ajax-search'],
                $model->warehouse_id
                    ? [$model->warehouse_id => WarehouseSearch::getNameById($model->warehouse_id)]
                    : [],
                [
                    'placeholder' => 'Choose warehouse',
                    'inputLength' => 0,
                    'onchange' => 'this.form.submit()'
                ]
            ); ?>
            <hr>
            <?= $form->field($model, 'reason_id')->select2Ajax(
                ['/reasons/ajax-search'],
                $model->reason_id
                    ? [$model->reason_id => Reason::getNameById($model->reason_id)]
                    : [],
                [
                    'placeholder' => 'Choose reason',
                    'inputLength' => 0,
                    'onchange' => 'this.form.submit()'
                ]
            ); ?>
            <?= $form->field($model, 'comment'); ?>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>
                    <?= $model->getAttributeLabel('amount'); ?>
                </label>
                <div class="row">
                    <div class="col-2 text-center">
                        <span>
                            <?= $model->getAttributeLabel('amount_from'); ?>
                        </span>
                    </div>
                    <div class="col-4">
                        <?= $form->field($model, 'amount_from')->numberInput()->label(false); ?>
                    </div>
                    <div class="col-2 text-center">
                        <span>
                            <?= $model->getAttributeLabel('amount_to'); ?>
                        </span>
                    </div>
                    <div class="col-4">
                        <?= $form->field($model, 'amount_to')->numberInput()->label(false); ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <?= $model->getAttributeLabel('expected_amount'); ?>
                </label>
                <div class="row">
                    <div class="col-2 text-center">
                        <span>
                            <?= $model->getAttributeLabel('expected_amount_from'); ?>
                        </span>
                    </div>
                    <div class="col-4">
                        <?= $form->field($model, 'expected_amount_from')->numberInput()->label(false); ?>
                    </div>
                    <div class="col-2 text-center">
                        <span>
                            <?= $model->getAttributeLabel('expected_amount_to'); ?>
                        </span>
                    </div>
                    <div class="col-4">
                        <?= $form->field($model, 'expected_amount_to')->numberInput()->label(false); ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <?= $model->getAttributeLabel('date'); ?>
                </label>
                <div class="row">
                    <div class="col-2 text-center">
                        <span><?= Yii::t('app', 'From'); ?></span>
                    </div>
                    <div class="col-4">
                        <?= $form->field($model, 'date_from')->datePicker()->label(false); ?>
                    </div>
                    <div class="col-2 text-center">
                        <span><?= Yii::t('app', 'To'); ?></span>
                    </div>
                    <div class="col-4">
                        <?= $form->field($model, 'date_to')->datePicker()->label(false); ?>
                    </div>
                </div>
            </div>

            <hr>

            <?= $form->field($model, 'status')->select2($model->getStatusesMap(), [
                'placeholder' => 'Choose status',
                'pluginOptions' => ['dropdownParent' => '#transactionSearchForm']
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <?php if (Yii::$app->getRequest()->get('TransactionsSearch')): ?>
                <?= Html::a(
                    FAS::icon('times') . '&ensp;' .
                    Yii::t('app', 'Reset filter'),
                    Url::remove('TransactionsSearch'),
                    ['class' => 'button is-danger is-small']
                ); ?>
            <?php endif; ?>
            <?= Html::submitButton(
                FAS::icon('search') . '&ensp;' .
                Yii::t('app', 'Search'),
                ['class' => 'button is-success']
            ); ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
