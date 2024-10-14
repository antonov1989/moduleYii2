<?php

namespace modules\warehouse\providers\query;

use common\models\ArticleTransaction;
use common\models\ArticleTransactionAction;
use modules\core\providers\query\BaseQueryProvider;
use modules\warehouse\searches\WarehouseTransactionSearch;
use yii\db\ActiveQuery;
use yii\db\Expression;

class WarehouseTransactionQueryProvider extends BaseQueryProvider
{
    public function getQuery(): ActiveQuery
    {
        return ArticleTransactionAction::find();
    }

    public function getQueryBySearchModel(WarehouseTransactionSearch $warehouseSearch): ActiveQuery
    {
        $query = $this->getQuery()
            ->alias('ita')
            ->with([
                       'transaction',
                       'article' => function (ActiveQuery $query): void {
                           $query->with('unit');
                       },
                       'uniqueArticle'
                   ])
            ->where(['ita.warehouse_id' => $warehouseSearch->current_warehouse_id])
            ->orderBy(['ita.created_at' => SORT_DESC]);

        $query->andFilterWhere(['ita.type' => $warehouseSearch->type]);
        $query->andFilterWhere(['ita.article_id' => $warehouseSearch->article_id]);
        $query->andFilterWhere(['ita.warehouse_id' => $warehouseSearch->warehouse_id]);

        // Amount
        $query->andFilterWhere(['>=', 'ita.amount', $warehouseSearch->amount_from]);
        $query->andFilterWhere(['<=', 'ita.amount', $warehouseSearch->amount_to]);
        // Amount target
        $query->andFilterWhere(['>=', 'ita.expected_amount', $warehouseSearch->expected_amount_from]);
        $query->andFilterWhere(['<=', 'ita.expected_amount', $warehouseSearch->expected_amount_to]);
        // Date
        $query->andFilterWhere(['>=', 'ita.date', $warehouseSearch->date_from]);
        $query->andFilterWhere(['<=', 'ita.date', $warehouseSearch->date_to]);

        $query->andFilterWhere(['ita.reason_id' => $warehouseSearch->reason_id]);
        $query->andFilterWhere([
            'ILIKE',
            new Expression('lower(ita.comment)'),
            $warehouseSearch->comment
        ]);

        $query->andFilterWhere(['ita.status' => $warehouseSearch->status]);

        $query->joinWith(['uniqueArticle']);
        $query->andFilterWhere(['ILIKE', 'unique_article.serial_number', $warehouseSearch->unique_article_serial]);
        $query->andFilterWhere(['ILIKE', 'unique_article.code', $warehouseSearch->unique_article_code]);

        return $query;
    }
}
