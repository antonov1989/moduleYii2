<?php

namespace modules\warehouse\services;

use common\models\ArticleAmount;
use modules\core\factories\ResourceFactory;
use modules\core\providers\data\DataProviderFactory;
use modules\warehouse\searches\WarehouseTransactionSearch;
use modules\warehouse\providers\query\WarehouseTransactionQueryProvider;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class WarehouseTransactionService
{
    public function __construct(
        private readonly DataProviderFactory $dataProviderFactory,
        private readonly WarehouseTransactionQueryProvider $warehouseQueryProvider,
    ) {
    }

    public function getActiveDataProvider(WarehouseTransactionSearch $warehouseSearch): ActiveDataProvider
    {
        $query = $this->warehouseQueryProvider->getQueryBySearchModel($warehouseSearch);

        return $this->dataProviderFactory->createActiveDataProviderByQuery($query);
    }

    public function getArticleTotalsByWarehouseId(string $warehouseId): array
    {
        $itemAmount = ArticleAmount::find()
            ->select([
                 'amount' => new Expression('sum(amount)'),
                 'reserved' => new Expression('sum(reserve_amount)')
             ])
            ->where(['warehouse_id' => $warehouseId])
            ->asArray()
            ->one();

        return [
            'amount' => $itemAmount['amount'] ?? 0,
            'reserved' => $itemAmount['reserved'] ?? 0
        ];
    }
}
