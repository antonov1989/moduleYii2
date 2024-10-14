<?php

namespace modules\warehouse\entities;

use common\models\ArticleAmount;
use common\models\EntityActiveRecord;
use common\models\NotificationsSettings;
use Iwms\Core\User\Entities\User;
use Iwms\Core\Group\Entities\Group;
use modules\commonTm\entities\BaseActiveRecord;
use Iwms\Core\General\Interfaces\DisplayedNameInterface;
use modules\employee\warehouseEmployee\entities\WarehouseEmployee;
use modules\group\enums\GroupType;
use modules\project\entities\ProjectWarehouse;
use modules\warehouse\entities\validators\UniqueNamePerEntityTypeValidator;
use Iwms\Core\WarehouseType\Entities\WarehouseType;
use siot\core\db\ActiveRecord;
use siot\general\settings\Fields;
use siot\general\validators\UuidValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * @property string $id [uuid]
 * @property string $type_id [uuid]
 * @property string $name [varchar(255)]
 * @property string $address [varchar(255)]
 * @property string $zip [varchar(255)]
 * @property string $city [varchar(255)]
 * @property string $number [varchar(255)]
 * @property integer $group_id [integer]
 * @property int $status [smallint]
 * @property int $created_at [integer]
 * @property int $updated_at [integer]
 * @property int $deleted_at [integer]
 * @property string $email
 *
 * @property WarehouseZone[] $zones
 * @property Group $group
 * @property ArticleAmount[] $articleAmounts
 * @property User[] $responsibilities
 * @property NotificationsSettings[] $notificationsSettings
 * @property WarehouseEntity $warehouseEntity
 * @property WarehouseType $warehouseType
 */
class Warehouse extends BaseActiveRecord implements DisplayedNameInterface
{
    /**
     * Counter types
     *
     * @var int
     */
    const NO_COUNTER_TYPE = 1;
    const UNIQUE_COUNTER_TYPE = 2;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['type_id', 'required'],
            ['type_id', 'trim'],
            ['type_id', UuidValidator::class],
            [
                'type_id',
                'exist',
                'targetClass' => WarehouseType::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', ActiveRecord::STATUS_DELETED]);
                },
            ],

            ['name', 'required'],
            ['name', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['name', UniqueNamePerEntityTypeValidator::class],

            ['address', 'required'],
            ['address', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['address', 'default', 'value' => null],

            ['zip', 'required'],
            ['zip', 'string', 'max' => Fields::INT_MAX],
            ['zip', 'default', 'value' => null],

            ['city', 'required'],
            ['city', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['city', 'default', 'value' => null],

            ['number', 'string', 'max' => Fields::VARCHAR_LENGTH],
            ['number', 'default', 'value' => null],

            ['group_id', 'integer'],
            ['group_id', 'default', 'value' => null],
            [
                'group_id',
                'exist',
                'targetClass' => Group::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', ActiveRecord::STATUS_DELETED]);
                    $query->andWhere(['type' => GroupType::warehouses->value]);
                },
            ],

            ['status', 'in', 'range' => $this->getStatuses()],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],

            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'number' => Yii::t('app', 'Designation number'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getZones(): ActiveQuery
    {
        return $this->hasMany(WarehouseZone::class, ['warehouse_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWarehouseEntity(): ActiveQuery
    {
        return $this->hasOne(WarehouseEntity::class, ['warehouse_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjectWarehouse(): ActiveQuery
    {
        return $this->hasOne(ProjectWarehouse::class, ['warehouse_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWarehouseType(): ActiveQuery
    {
        return $this->hasOne(WarehouseType::class, ['id' => 'type_id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getResponsibilities(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'responsible_id'])
            ->viaTable('warehouse_responsible', ['warehouse_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArticleAmounts(): ActiveQuery
    {
        return $this->hasMany(ArticleAmount::class, ['warehouse_id' => 'id']);
    }

    public function getNotificationsSettings(): ActiveQuery
    {
        return $this->hasMany(NotificationsSettings::class, ['entity_id' => 'id'])
            ->andWhere(['entity_type' => EntityActiveRecord::ENTITY_TYPE_WAREHOUSE])
            ->andWhere(['deleted_at' => null]);
    }

    public function getEmployees(): ActiveQuery
    {
        return $this->hasMany(WarehouseEmployee::class, ['warehouse_id' => 'id']);
    }

    /**
     * @return void
     */
    public function prepareModelForDelete(): void
    {
        $this->setScenario(ActiveRecord::SCENARIO_DELETE);
        $this->status = ActiveRecord::STATUS_DELETED;
    }

    public static function getNameById(string $id): string
    {
        return (string) self::find()->select(['name'])->where(['id' => $id])->scalar();
    }

    public function getDisplayedName(): string
    {
        return $this->name;
    }
}
