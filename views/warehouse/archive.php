<?php

use frontend\widgets\FilterGroups;
use frontend\widgets\SingleLinkPager;
use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\core\permissions\Permissions;
use modules\group\enums\GroupType;
use modules\warehouse\controllers\actions\warehouse\WarehouseArchiveAction;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\models\WarehouseCreateModel;
use modules\warehouse\searches\ArchiveWarehouseSearch;
use siot\general\grid\GridView;
use siot\general\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @uses WarehouseArchiveAction
 *
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var WarehouseCreateModel $warehouseCreateModel
 * @var ArchiveWarehouseSearch $archiveWarehouseSearch
 * @var bool $showSearch
 * @var array $parentEntitiesMap
 * @var array $warehouseTypesMap
 */

$entityType = $archiveWarehouseSearch->entity_type;

$this->title = Yii::t('app', ucfirst($entityType) . ' warehouses archive');

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];

$menu = [
    Html::a(
        Yii::t('app', 'All warehouses'),
        $moduleRouter->toModule('index', [$entityType, 'warehouse']),
        [
            'class' => 'link-nav font-weight-bold',
            'icon' => 'list-ul',
        ]
    ),
    function () use ($entityType, $moduleRouter): string {
        if (!Permissions::getApp()->warehouse()->archive()->read()->can()) {
            return '';
        }

        return Html::a(
            Yii::t('app', 'Archive'),
            $moduleRouter->toModule('archive', [$entityType, 'warehouse']),
            [
                'class' => 'link-nav font-weight-bold active',
                'icon' => 'archive',
            ]
        );
    },
];

$pjaxContainer = 'warehouseArchivePjaxLoaderId';

?>
<div class="heading d-flex justify-content-between align-items-center">
    <h4 class="heading-title"><?= $this->title ?></h4>
</div>

<?= $this->renderFile('@partial/_index-navigation.php', ['menu' => $menu]); ?>

<div class="content-wrapper">
    <div class="content">
        <div class="row mb-4">
            <div class="col">
                <!-- Filter button -->
                <?= Html::collapseButton(
                    Html::span(Yii::t('app', 'Filter'), ['class' => 'd-none d-md-inline']),
                    '#filterForm',
                    ['class' => 'button-modernize button-secondary me-2', 'icon' => 'filter']
                ); ?>

            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4 collapse <?= $showSearch ? 'show' : ''; ?>" id="filterForm">
            <div class="row mt-4">
                <div class="col-12">
                    <?= $this->render('_search', [
                        'searchModel' => $archiveWarehouseSearch,
                        'showSearch' => $showSearch,
                        'routeWarehouseAjaxSearch' => $moduleRouter->toModule(
                            'search.ajax-search',
                            [$entityType, 'warehouse']
                        ),
                        'parentEntitiesMap' => $parentEntitiesMap,
                        'warehouseTypesMap' => $warehouseTypesMap,
                    ]); ?>
                </div>
            </div>
        </div>

        <!-- Records -->
        <div class="content-inner">
            <div class="row mt-2">
                <div class="col-xl-3">
                    <h4>
                        <?= Yii::t('app', 'Groups'); ?>
                    </h4>

                    <?= FilterGroups::widget([
                        'type' => [GroupType::warehouses->value],
                        'filteredGroups' => $archiveWarehouseSearch->group_ids,
                        'entityTable' => Warehouse::tableName(),
                    ]); ?>
                </div>
                <div class="col-xl-9 mt-5 mt-xl-0">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'rowClickUrl' => function (Warehouse $warehouse, string $id) use ($moduleRouter): string {
                            return $moduleRouter->toModule(
                                'control.info',
                                [$warehouse->warehouseEntity->entity_type => $warehouse->warehouseEntity->entity_id, 'warehouse' => $id]
                            );
                        },
                        'pager' => [
                            'class' => SingleLinkPager::class,
                            'pjaxContainer' => $pjaxContainer,
                            'model' => $archiveWarehouseSearch,
                        ],
                        'columns' => [
                            ['class' => SerialColumn::class],
                            'name',
                            [
                                'label' => Yii::t('app', ucfirst($entityType)),
                                'format' => 'raw',
                                'value' => function (Warehouse $model) use ($parentEntitiesMap, $moduleRouter): string {
                                    return Html::a(
                                        $parentEntitiesMap[$model->warehouseEntity->entity_id] ?? ' - ',
                                        $moduleRouter->toModule(
                                            'control.info',
                                            [$model->warehouseEntity->entity_type => $model->warehouseEntity->entity_id]
                                        ),
                                        ['class' => 'button is-gray is-light is-small']
                                    );
                                },
                            ],
                            [
                                'attribute' => 'type_id',
                                'format' => 'raw',
                                'value' => function (Warehouse $model): string {
                                    return $model->warehouseType->status === $model->warehouseType::STATUS_DELETED
                                        ? '<i class="text-danger fas fa-minus"></i>'
                                        : $model->warehouseType->name;
                                }
                            ],
                            'number',
                            'address',
                            'zip',
                            'city',
                            [
                                'attribute' => 'group_id',
                                'format' => 'raw',
                                'value' => function (Warehouse $model): string {
                                    if ($model->group) {
                                        $content = $model->group->name;
                                        $content .= Html::span(
                                            '',
                                            [
                                                'style' => 'background-color:' . $model->group->color,
                                                'class' => 'category-color'
                                            ]
                                        );

                                        return $content;
                                    }

                                    return Html::render();
                                }
                            ],
                            [
                                'attribute' => 'deleted_at',
                                'label' => Yii::t('app', 'Archived date'),
                                'format' => ['date'],
                            ],
                        ],
                    ]); ?>

                    <?php
                        Pjax::begin([
                            'enablePushState' => false,
                            'timeout' => 0,
                            'options' => [
                                'id' => $pjaxContainer,
                            ],
                        ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
