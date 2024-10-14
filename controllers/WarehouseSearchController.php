<?php

namespace modules\warehouse\controllers;

use modules\commonTm\interfaces\EntityTypeProviderInterface;
use modules\core\controllers\BaseController;
use modules\core\permissions\Permissions;
use modules\warehouse\controllers\actions\search\WarehouseAjaxSearchAction;
use modules\warehouse\controllers\actions\search\WarehouseParentTypeAjaxSearchAction;
use modules\warehouse\controllers\actions\search\WarehouseSearchActionIds;
use yii\filters\AccessControl;

class WarehouseSearchController extends BaseController
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
            WarehouseSearchActionIds::ajaxSearch->value => WarehouseAjaxSearchAction::class,
            WarehouseSearchActionIds::parentTypeAjaxSearch->value => WarehouseParentTypeAjaxSearchAction::class,
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
                            WarehouseSearchActionIds::ajaxSearch->value,
                            WarehouseSearchActionIds::parentTypeAjaxSearch->value,
                        ],
                        'allow' => true,
                        'roles' => Permissions::getApp()->warehouse()->read()->getArray(),
                    ],
                ],
            ],
        ];
    }
}
