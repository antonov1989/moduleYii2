<?php

namespace modules\warehouse\controllers;

use modules\commonTm\controllers\ModuleController;
use modules\commonTm\interfaces\EntityTypeProviderInterface;
use modules\core\permissions\Permissions;
use modules\warehouse\controllers\actions\warehouse\{
    WarehouseActionIds,
    WarehouseIndexAction,
    WarehouseCreateAction,
    WarehouseValidateCreateAction,
    WarehouseArchiveAction,
};
use yii\filters\AccessControl;

class WarehouseController extends ModuleController
{
    public EntityTypeProviderInterface $entityTypeProvider;

    /**
     * Injected value from config
     * @var string [ModuleTypeEnum::customer->value, ModuleTypeEnum::company->value, etc]
     */
    public string $warehouseType;

    public function init(): void
    {
        parent::init();

        $this->warehouseType = $this->module->params['parentEntityType'];

        /** @var EntityTypeProviderInterface $provider */
        $provider = \Yii::createObject($this->module->params['entityTypeProvider']);
        $this->entityTypeProvider = $provider;
    }

    public function actions(): array
    {
        return [
            WarehouseActionIds::INDEX => WarehouseIndexAction::class,
            WarehouseActionIds::CREATE => WarehouseCreateAction::class,
            WarehouseActionIds::VALIDATE_CREATE => WarehouseValidateCreateAction::class,

            WarehouseActionIds::ARCHIVE => WarehouseArchiveAction::class,
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
                            WarehouseActionIds::INDEX,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->read()->getArray(),
                    ],
                    [
                        'actions' => [
                            WarehouseActionIds::CREATE,
                            WarehouseActionIds::VALIDATE_CREATE,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->create()->getArray(),
                    ],
                    [
                        'actions' => [WarehouseActionIds::ARCHIVE],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->archive()->read()->getArray(),
                    ],
                ],
            ],
        ];
    }
}
