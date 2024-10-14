<?php

namespace modules\warehouse\searches;

use common\models\{
    Article,
    ArticleTransaction,
};
use Iwms\Core\Reason\Entities\Reason;
use modules\warehouse\entities\Warehouse;
use siot\general\settings\Fields;
use siot\general\validators\UuidValidator;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class TransactionsSearch
 */
class WarehouseTransactionSearch extends ArticleTransaction
{
    public int|string|null $type = null;
    public ?string $article_id = null;
    public ?string $current_warehouse_id = null;
    public ?string $warehouse_id = null;
    public int|string|null $amount_from = null;
    public int|string|null $amount_to = null;
    public int|string|null $expected_amount_from = null;
    public int|string|null $expected_amount_to = null;
    public ?string $date_from = null;
    public ?string $date_to = null;
    public ?string $date_target_from = null;
    public ?string $date_target_to = null;
    public ?string $reason_id = null;
    public ?string $comment = null;
    public int|string|null $status = null;
    public ?string $unique_article_serial = null;
    public ?string $unique_article_code = null;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'article_transaction';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'type',
                'in',
                'range' => $this->getTypes()
            ],

            ['article_id', UuidValidator::class],
            [
                'article_id',
                'exist',
                'targetClass' => Article::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', Article::STATUS_DELETED]);
                }
            ],

            ['current_warehouse_id', UuidValidator::class],

            ['warehouse_id', UuidValidator::class],
            [
                'warehouse_id',
                'exist',
                'targetClass' => Warehouse::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', Warehouse::STATUS_DELETED]);
                }
            ],

            ['amount_from', 'integer', 'min' => Fields::INT_MIN, 'max' => Fields::INT_MAX],

            ['amount_to', 'integer', 'min' => Fields::INT_MIN, 'max' => Fields::INT_MAX],

            ['expected_amount_from', 'integer', 'min' => Fields::INT_MIN, 'max' => Fields::INT_MAX],

            ['expected_amount_to', 'integer', 'min' => Fields::INT_MIN, 'max' => Fields::INT_MAX],

            ['date_from', 'date'],

            ['date_to', 'date'],

            ['reason_id', UuidValidator::class],
            [
                'reason_id',
                'exist',
                'targetClass' => Reason::class,
                'targetAttribute' => 'id',
                'filter' => function (ActiveQuery $query): void {
                    $query->andWhere(['!=', 'status', Reason::STATUS_DELETED]);
                }
            ],

            ['comment', 'string'],
            ['comment', 'trim'],

            ['status', 'in', 'range' => $this->getStatuses()],

            ['unique_article_serial', 'string'],

            ['unique_article_code', 'string']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels['article_id'] = Yii::t('app', 'Product');
        $labels['warehouse_id'] = Yii::t('app', 'Warehouse');
        $labels['amount'] = Yii::t('app', 'Amount');
        $labels['amount_from'] = Yii::t('app', 'From');
        $labels['amount_to'] = Yii::t('app', 'To');
        $labels['expected_amount'] = Yii::t('app', 'Expected Amount');
        $labels['expected_amount_from'] = Yii::t('app', 'From');
        $labels['expected_amount_to'] = Yii::t('app', 'To');
        $labels['date_from'] = Yii::t('app', 'Date (From)');
        $labels['date_to'] = Yii::t('app', 'Date (To)');
        $labels['unique_article_serial'] = Yii::t('app', 'Item serial number');
        $labels['unique_article_code'] = Yii::t('app', 'Item code');

        return $labels;
    }

    /**
     * @return array
     */
    public function attributeHints(): array
    {
        return [
            'article_id' => Yii::t('app', 'Search by \'Serial number\' or \'Code\'')
        ];
    }
}
