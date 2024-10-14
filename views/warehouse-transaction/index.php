<?php

use common\models\ArticleTransactionAction;
use frontend\components\web\View;
use modules\warehouse\searches\WarehouseTransactionSearch;
use frontend\widgets\PopoverX;
use rmrevin\yii\fontawesome\FAS;
use siot\general\grid\GridView;
use siot\general\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;

/**
 * @var View $this
 * @var WarehouseTransactionSearch $searchModel
 * @var null $filterDatesModel
 * @var ActiveDataProvider $dataProvider
 * @var int $totalAmount
 * @var int $totalReservedAmount
 */

$this->title = Yii::t('app', 'Transactions');
$showSearch = Yii::$app->getRequest()->get('TransactionsSearch') !== null;
$this->fullSize = true;
$createInvoiceButtonDisabled = true;
if (
    !empty($searchModel->type) &&
    (!empty($searchModel->date_target_from) || $searchModel->date_target_to)
) {
    $createInvoiceButtonDisabled = false;
}

?>

<div class="container">
    <div class="row">
        <div class="col-6">
            <div class="card text-white bg-secondary mb-3 mx-auto" style="max-width: 18rem;">
                <div class="card-header">
                    <h5 class="card-title text-center mb-0">
                        <?= Yii::t('app', 'Total amount'); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <h4 class="card-text text-center">
                        <?= $totalAmount; ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card text-white bg-secondary mb-3 mx-auto" style="max-width: 18rem;">
                <div class="card-header">
                    <h5 class="card-title text-center mb-0">
                        <?= Yii::t('app', 'Total reserved amount'); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <h4 class="card-text text-center">
                        <?= $totalReservedAmount; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col d-flex align-items-center justify-content-end" style="flex-basis: content;">
        <div class="me-3">
            <?= Html::collapseButton(
                Yii::t('app', 'Filter'),
                '#filterForm',
                [
                    'class' => 'button is-gray pull-right',
                    'icon' => 'filter'
                ]
            ); ?>
        </div>
    </div>
    <div class="col-12">
        <div class="row mt-2 collapse <?php if ($showSearch): ?> show<?php endif; ?>" id="filterForm">
            <div class="col-md-12">
                <?= $this->render('_search', [
                    'model' => $searchModel
                ]); ?>
            </div>
        </div>
    </div>
</div>

<hr>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function (ArticleTransactionAction $model): array {
        if (
            $model->type === ArticleTransactionAction::TYPE_IN
        ) {
            $class = 'row-success';
        } else {
            $class = 'row-danger';
        }

        return ['class' => $class];
    },
    'columns' => [
        ['class' => SerialColumn::class],
        [
            'label' => Yii::t('app', 'Type'),
            'attribute' => 'type',
            'value' => function (ArticleTransactionAction $model): string {
                return $model->transaction->getTypesMap()[$model->transaction->type];
            }
        ],
        [
            'attribute' => 'article.name',
            'format' => 'raw',
            'value' => function (ArticleTransactionAction $model): string {
                return Html::a(
                    "{$model->article->name} ({$model->article->serial_number})",
                    ["/article/$model->article_id/main/information"],
                    ['target' => '__blank']
                );
            }
        ],
        [
            'attribute' => 'amount',
            'format' => 'raw',
            'value' => function (ArticleTransactionAction $model): string {
                $value = (int) $model->transaction->amount;
                $value .= '&nbsp;';
                $value .= $model->article->unit->name;

                return $value;
            }
        ],
        [
            'attribute' => 'unique_article_id',
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-center'],
            'contentOptions' => ['class' => 'text-center'],
            'value' => function (ArticleTransactionAction $model): ?string {
                if (empty($model->unique_article_id)) {
                    return null;
                }
                $label = $model->uniqueArticle->serial_number;
                if (!empty($model->uniqueArticle->code)) {
                    $label .= " ({$model->uniqueArticle->code})";
                }

                return Html::a(
                    $label,
                    ["/article/{$model->uniqueArticle->article_id}/unicity#$model->unique_article_id"],
                    ['target' => '__blank']
                );
            }
        ],
        [
            'label' => Yii::t('app', 'Status'),
            'format' => 'raw',
            'value' => function (ArticleTransactionAction $model): string|null {
                $statuses = $model->transaction->getStatusesMap();
                if (!isset($statuses[$model->transaction->status])) {
                    return null;
                }
                $options = ['class' => 'text-' . $model->transaction->getColorByStatus()];

                return Html::span($statuses[$model->transaction->status], $options);
            }
        ],
        [
            'format' => 'raw',
            'value' => function (ArticleTransactionAction $model): string {
                if (empty($model->transaction->comment)) {
                    return '';
                }

                return PopoverX::widget([
                    'placement' => PopoverX::ALIGN_LEFT,
                    'closeButton' => false,
                    'content' => $model->transaction->comment,
                    'toggleButton' => [
                        'label' => FAS::icon('comment'),
                        'class' => 'button is-primary is-light is-square is-small',
                    ],
                    'pluginOptions' => [
                        'trigger' => 'hover'
                    ]
                ]);
            }
        ]
    ]
]); ?>
