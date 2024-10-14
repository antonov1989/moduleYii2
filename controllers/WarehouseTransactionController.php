<?php

namespace modules\warehouse\controllers;

use modules\commonTm\controllers\ModuleController;
use modules\core\permissions\Permissions;
use modules\warehouse\controllers\actions\transaction\WarehouseTransactionActionIds;
use modules\warehouse\controllers\actions\transaction\WarehouseTransactionIndexAction;
use yii\filters\AccessControl;

class WarehouseTransactionController extends ModuleController
{
    public $layout = 'entity';

    public function actions(): array
    {
        return [
            WarehouseTransactionActionIds::index->value => WarehouseTransactionIndexAction::class,
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
                            WarehouseTransactionActionIds::index->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->transactionRead()->getArray(),
                    ],
                ],
            ],
        ];
    }
}
