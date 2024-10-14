<?php

use frontend\widgets\FilterGroups;
use frontend\widgets\Modal;
use frontend\widgets\SingleLinkPager;
use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\core\permissions\Permissions;
use modules\group\enums\GroupType;
use modules\warehouse\controllers\actions\warehouse\WarehouseIndexAction;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\models\WarehouseCreateModel;
use modules\warehouse\searches\WarehouseSearch;
use siot\general\grid\GridView;
use siot\general\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\SerialColumn;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @uses WarehouseIndexAction
 *
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var WarehouseCreateModel $warehouseCreateModel
 * @var WarehouseSearch $warehouseSearch
 * @var bool $showSearch
 * @var array $groupsMap
 * @var array $warehouseTypesMap
 * @var array $parentEntitiesMap
 */

$entityType = $warehouseSearch->entity_type;

$this->title = Yii::t('app', ucfirst($entityType) . ' warehouses');

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];

$menu = [
    Html::a(
        Yii::t('app', 'All warehouses'),
        url: $moduleRouter->toModule('index', [$entityType, 'warehouse']),
        options: [
            'class' => 'link-nav font-weight-bold active',
            'icon' => 'list-ul',
        ]
    ),
    function () use ($entityType, $moduleRouter): string {
        if (!Permissions::getApp()->warehouse()->archive()->read()->can()) {
            return '';
        }

        return Html::a(
            Yii::t('app', 'Archive'),
            $moduleRouter->to('archive'),
            [
                'class' => 'link-nav font-weight-bold',
                'icon' => 'archive',
            ]
        );
    },
];

$pjaxContainer = 'warehousePjaxLoaderId';

?>

<div class="heading d-flex justify-content-between align-items-center">
    <h4 class="heading-title"><?= $this->title ?></h4>
    <?php
    if (Permissions::getApp()->warehouse()->create()->can()) : ?>
        <!-- Modal -->
        <?php
        Modal::begin([
            'title' => Yii::t('app', 'Create warehouse'),
            'options' => [
                'id' => 'createWarehouseModal',
                'data-clear-form' => '#createWarehouseForm',
            ],
            'headerOptions' => ['class' => 'modal-header'],
            'bodyOptions' => ['class' => 'modal-body'],
            'toggleButton' => [
                'label' => Html::span(
                    Yii::t('app', 'Create warehouse'),
                    ['class' => 'd-none d-md-inline']
                ),
                'icon' => 'plus',
                'class' => 'button-modernize button-primary',
            ],
            'size' => Modal::SIZE_LARGE,
        ]); ?>

        <?= $this->render('_create-warehouse', [
            'model' => $warehouseCreateModel,
            'groupsMap' => $groupsMap,
            'warehouseTypesMap' => $warehouseTypesMap,
            'parentEntitiesMap' => $parentEntitiesMap,
            'entityType' => $entityType,
        ]); ?>

        <?php
        Modal::end(); ?>
    <?php
    endif; ?>
</div>

<?= $this->renderFile('@partial/_index-navigation.php', ['menu' => $menu]); ?>

<div class="content-wrapper 111">
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
                        'searchModel' => $warehouseSearch,
                        'showSearch' => $showSearch,
                        'routeWarehouseAjaxSearch' => $moduleRouter->to('search.ajax-search'),
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
                        'filteredGroups' => $warehouseSearch->group_ids ?? [],
                        'entityTable' => Warehouse::tableName(),
                    ]); ?>
                </div>
                <div class="col-xl-9 mt-5 mt-xl-0">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'rowClickUrl' => function (Warehouse $warehouse, string $id) use ($moduleRouter): string {
                            return $moduleRouter->to('control.info', $id);
                        },
                        'pager' => [
                            'class' => SingleLinkPager::class,
                            'pjaxContainer' => $pjaxContainer,
                            'model' => $warehouseSearch,
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
                                    return $model->warehouseType->name;
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
                                'attribute' => 'created_at',
                                'label' => Yii::t('app', 'Created date'),
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
