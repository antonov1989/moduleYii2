<?php

namespace modules\warehouse\controllers;

use modules\commonTm\controllers\ModuleController;
use modules\commonTm\interfaces\EntityTypeProviderInterface;
use modules\core\permissions\Permissions;
use modules\warehouse\controllers\actions\control\{
    WarehouseControlActionIds,
    WarehouseControlInfoAction,
    WarehouseControlUpdateAction,
    WarehouseControlValidateUpdateAction,
};
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class WarehouseControlController extends ModuleController
{
    public $layout = 'entity';

    public EntityTypeProviderInterface $entityTypeProvider;

    /**
     * Injected value from config
     * @var string [ModuleTypeEnum::customer->value, ModuleTypeEnum::company->value, etc]
     */
    public string $warehouseType;

    public function init(): void
    {
        $this->warehouseType = $this->module->params['parentEntityType'];

        /** @var EntityTypeProviderInterface $provider */
        $provider = \Yii::createObject($this->module->params['entityTypeProvider']);
        $this->entityTypeProvider = $provider;

        parent::init();
    }

    public function actions(): array
    {
        return [
            WarehouseControlActionIds::info->value => WarehouseControlInfoAction::class,
            WarehouseControlActionIds::update->value => WarehouseControlUpdateAction::class,
            WarehouseControlActionIds::validateUpdate->value => WarehouseControlValidateUpdateAction::class,
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
                            WarehouseControlActionIds::info->value,
                            WarehouseControlActionIds::update->value,
                            WarehouseControlActionIds::validateUpdate->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->update()->getArray(),
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    WarehouseControlActionIds::update->value => ['post'],
                    WarehouseControlActionIds::validateUpdate->value => ['post'],
                ],
            ],
        ];
    }
}
