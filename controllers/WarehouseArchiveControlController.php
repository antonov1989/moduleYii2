<?php

namespace modules\warehouse\controllers;

use modules\commonTm\controllers\ModuleController;
use modules\core\permissions\Permissions;
use modules\warehouse\controllers\actions\archive\WarehouseArchiveControlActionIds;
use modules\warehouse\controllers\actions\archive\WarehouseArchiveControlCreateArchiveAction;
use modules\warehouse\controllers\actions\archive\WarehouseArchiveControlRestoreAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class WarehouseArchiveControlController extends ModuleController
{
    public function actions(): array
    {
        return [
            WarehouseArchiveControlActionIds::createArchive->value => WarehouseArchiveControlCreateArchiveAction::class,
            WarehouseArchiveControlActionIds::restore->value => WarehouseArchiveControlRestoreAction::class,
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
                            WarehouseArchiveControlActionIds::createArchive->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->archive()->create()->getArray(),
                    ],
                    [
                        'actions' => [
                            WarehouseArchiveControlActionIds::restore->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->archive()->restore()->getArray(),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    WarehouseArchiveControlActionIds::createArchive->value => ['post'],
                    WarehouseArchiveControlActionIds::restore->value => ['post'],
                ],
            ],
        ];
    }
}
