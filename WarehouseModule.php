<?php

namespace modules\warehouse;

use Iwms\Core\General\Providers\Data\GetEntityProviderInterface;
use Iwms\Core\General\Providers\Data\ModuleEntityProviderInterface;
use modules\commonTm\modules\BaseTmModule;
use modules\commonTm\traits\ModuleCurrentDir;
use modules\core\permissions\GroupPermissions;
use modules\core\permissions\ModulePermissionsInterface;
use modules\core\permissions\Permissions;
use modules\warehouse\repositories\WarehouseRepository;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

/**
 * Class WarehouseModule
 * @package frontend\modules\warehouse
 */
class WarehouseModule extends BaseTmModule implements ModulePermissionsInterface, GetEntityProviderInterface
{
    use ModuleCurrentDir;

    public function init(): void
    {
        if (
            empty($this->params['parentEntityType'])
            || empty($this->params['entityTypeProvider'])
        ) {
            throw new InvalidConfigException(
                \Yii::t('app', 'Specify "parentEntityType" and "entityTypeProvider" in module configuration')
            );
        }

        parent::init();
    }

    public function getDefaultPermissions(): GroupPermissions
    {
        return Permissions::getApp()->warehouse();
    }

    /**
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public function getEntityProvider(): ModuleEntityProviderInterface
    {
        return Yii::$container->get(WarehouseRepository::class);
    }
}
