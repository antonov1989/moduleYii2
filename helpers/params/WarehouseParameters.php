<?php

namespace modules\warehouse\helpers\params;

use modules\commonTm\helpers\params\ModuleParameters;

use Yii;

class WarehouseParameters extends ModuleParameters
{
    /**
     * @throws ModuleNotFoundException
     */
    public function __construct()
    {
        $module = Yii::$app->getModule('warehouse');
        if (!$module) {
            throw new ModuleNotFoundException();
        }

        parent::__construct($module->params);
    }
}
