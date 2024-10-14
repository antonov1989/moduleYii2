<?php

namespace modules\warehouse\controllers;

use modules\commonTm\controllers\ModuleController;
use modules\core\permissions\Permissions;
use modules\warehouse\controllers\actions\zone\WarehouseZoneActionIds;
use modules\warehouse\controllers\actions\zone\WarehouseZoneDeleteAction;
use modules\warehouse\controllers\actions\zone\WarehouseZoneIndexAction;
use modules\warehouse\controllers\actions\zone\WarehouseZoneCreateAction;
use modules\warehouse\controllers\actions\zone\WarehouseZoneUpdateAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class WarehouseZoneController extends ModuleController
{
    public $layout = 'entity';

    public function actions(): array
    {
        return [
            WarehouseZoneActionIds::index->value => WarehouseZoneIndexAction::class,
            WarehouseZoneActionIds::create->value => WarehouseZoneCreateAction::class,
            WarehouseZoneActionIds::update->value => WarehouseZoneUpdateAction::class,
            WarehouseZoneActionIds::delete->value => WarehouseZoneDeleteAction::class,
        ];
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            WarehouseZoneActionIds::index->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->zoneRead()->getArray(),
                    ],
                    [
                        'actions' => [
                            WarehouseZoneActionIds::create->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->zoneCreate()->getArray(),
                    ],
                    [
                        'actions' => [
                            WarehouseZoneActionIds::update->value,
                        ],
                        'allow' => true,
                        'roles' => ['warehouse_zones_update'],
                    ],
                    [
                        'actions' => [
                            WarehouseZoneActionIds::delete->value,
                        ],
                        'allow' => true,
                        'roles' => ['warehouse_zones_delete'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    WarehouseZoneActionIds::create->value => ['get', 'post'],
                    WarehouseZoneActionIds::update->value => ['get', 'post'],
                    WarehouseZoneActionIds::delete->value => ['post'],
                ],
            ],
        ];
    }
}
