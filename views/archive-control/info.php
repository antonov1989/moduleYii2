<?php

use common\models\Article;
use common\models\ArticleAmount;
use common\models\ArticleTransaction;
use common\models\Order;
use frontend\helpers\ModuleRoute;
use rmrevin\yii\fontawesome\FAS;
use siot\general\grid\GridView;
use siot\general\grid\SerialColumn;
use siot\general\helpers\Html;
use yii\data\ActiveDataProvider;

/**
 * @var ActiveDataProvider $dataProviderArticleStock
 * @var ActiveDataProvider $dataProviderOrders
 * @var ActiveDataProvider $dataProviderTransactions
 * @var ActiveDataProvider $dataProviderArticlesConsolidated
 */

$this->title = Yii::t('app', 'Warehouse Archiving Restrictions')

?>
<?php if ($dataProviderArticleStock->totalCount > 0): ?>
    <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
        <?= FAS::icon('exclamation-triangle')->size(FAS::SIZE_2X); ?> 
        <?= Yii::t('app', 'Warehouse cannot be archived. It has existing products stock level(s)') ?>
    </div>

    <div class="row">
        <div class="col">
            <?= GridView::widget([
                'dataProvider' => $dataProviderArticleStock,
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'label' => Yii::t('app', 'Name'),
                        'value' => function (ArticleAmount $articleAmount): string {
                            return Html::a(
                                $articleAmount->article->name,
                                ModuleRoute::toArticle($articleAmount->article_id, '/main/information'),
                                ['class' => 'link', 'target' => '__blank']
                            );
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => Yii::t('app', 'Group'),
                        'value' => function (ArticleAmount $articleAmount): string|null {
                            return $articleAmount->article->group?->name;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Total amount'),
                        'value' => function (ArticleAmount $articleAmount): int {
                            return $articleAmount->amount + $articleAmount->reserve_amount;
                        }
                    ]
                ]
            ]); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($dataProviderOrders->totalCount > 0): ?>
    <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
        <?= FAS::icon('exclamation-triangle')->size(FAS::SIZE_2X); ?> 
        <?= Yii::t('app', 'Warehouse cannot be archived. It has existing order transaction(s)') ?>
    </div>

    <div class="row">
        <div class="col">
            <?= GridView::widget([
                'dataProvider' => $dataProviderOrders,
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'label' => Yii::t('app', 'Order number'),
                        'value' => function (Order $order): string {
                            return Html::a(
                                $order->order_number,
                                ModuleRoute::toOrder($order->id, '/main/information'),
                                ['class' => 'link', 'target' => '__blank']
                            );
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => Yii::t('app', 'Type'),
                        'value' => function (Order $order): string {
                            return $order->getTypesMap($order->type);
                        }
                    ],
                    'delivery_date',
                    [
                        'label' => Yii::t('app', 'Status'),
                        'value' => function (Order $order): string|null {
                            return $order->getStatusesMap()[$order->status] ?? null;
                        }
                    ],
                ]
            ]); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($dataProviderTransactions->totalCount > 0): ?>
    <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
        <?= FAS::icon('exclamation-triangle')->size(FAS::SIZE_2X); ?> 
        <?= Yii::t('app', 'Warehouse cannot be archived. It has existing transaction(s)') ?>
    </div>

    <div class="row">
        <div class="col">
            <?= GridView::widget([
                'dataProvider' => $dataProviderTransactions,
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'label' => Yii::t('app', 'From WH'),
                        'value' => function (ArticleTransaction $articleTransaction): string|null {
                            return $articleTransaction->warehouseFrom?->name;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'To WH'),
                        'value' => function (ArticleTransaction $articleTransaction): string|null {
                            return $articleTransaction->warehouseTo?->name;
                        }
                    ],
                    'date_shipping',
                    'amount',
                    [
                        'label' => Yii::t('app', 'Status'),
                        'value' => function (ArticleTransaction $articleTransaction): string|null {
                            return $articleTransaction->getStatusesMap()[$articleTransaction->status] ?? null;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Type'),
                        'value' => function (ArticleTransaction $articleTransaction): string|null {
                            return $articleTransaction->getTypesMap()[$articleTransaction->type] ?? null;
                        }
                    ],
                ]
            ]); ?>
        </div>
    </div>
<?php endif; ?>


<?php if ($dataProviderArticlesConsolidated->totalCount > 0): ?>
    <div class="alert alert-warning alert-dismissible d-flex align-items-center" role="alert">
        <?= FAS::icon('exclamation-triangle')->size(FAS::SIZE_2X); ?> 
        <?= Yii::t('app', 'Warehouse cannot be archived. It is consolidated for specific product(s)') ?>
    </div>

    <div class="row">
        <div class="col">
            <?= GridView::widget([
                'dataProvider' => $dataProviderArticlesConsolidated,
                'columns' => [
                    ['class' => SerialColumn::class],
                    [
                        'label' => Yii::t('app', 'Name'),
                        'value' => function (Article $article): string {
                            return Html::a(
                                $article->name,
                                ModuleRoute::toArticle($article->id, '/main/information'),
                                ['class' => 'link', 'target' => '__blank']
                            );
                        },
                        'format' => 'raw'
                    ],
                    [
                        'label' => Yii::t('app', 'Group'),
                        'value' => function (Article $article): string|null {
                            return $article->group?->name;
                        }
                    ]
                ]
            ]); ?>
        </div>
    </div>
<?php endif; ?>
