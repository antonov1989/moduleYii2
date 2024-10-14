<?php

namespace modules\warehouse\providers\query;

use modules\core\providers\query\BaseQueryProvider;
use modules\warehouse\app\api\models\WarehouseSearch as ApiWarehouseSearch;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\searches\BaseWarehouseSearch;
use modules\warehouse\searches\WarehouseContractorDependsSearch;
use yii\db\ActiveQuery;

class WarehouseQueryProvider extends BaseQueryProvider
{
    public function getQuery(): ActiveQuery
    {
        return Warehouse::find();
    }

    public function getActiveWarehouseQuery(WarehouseContractorDependsSearch $warehouseSearch): ActiveQuery
    {
        $query = $this->getQuery();

        $query->joinWith('warehouseEntity');

        $query->where(['warehouse_entity.entity_id' => $warehouseSearch->entity_ids]);
        $query->andWhere(['warehouse_entity.entity_type' => $warehouseSearch->entity_type]);
        $query->andWhere(['status' => Warehouse::STATUS_ACTIVE]);

        return $query;
    }

    public function getActiveQuery(): ActiveQuery
    {
        $query = $this->getQuery();

        $query->where(['status' => Warehouse::STATUS_ACTIVE]);

        return $query;
    }

    public function getWarehouseSearchQuery(ApiWarehouseSearch $warehouseSearch): ActiveQuery
    {
        $query = $this->getQuery();

        $query->joinWith('warehouseEntity')
            ->andWhere(['warehouse_entity.entity_type' => $warehouseSearch->entity_type])
            ->andFilterWhere(['warehouse_entity.entity_id' => $warehouseSearch->entity_id]);

        $query->andWhere(['status' => $warehouseSearch->status])
            ->andFilterWhere(['id' => $warehouseSearch->id])
            ->andFilterWhere(['ILIKE', 'name', $warehouseSearch->name]);

        return $query;
    }

    public function getQueryBySearchModel(BaseWarehouseSearch $warehouseSearch): ActiveQuery
    {
        $query = $this->getQuery();

        $query->joinWith('warehouseEntity');

        $query->andWhere(['status' => $warehouseSearch->status]);
        $query->andWhere(['warehouse_entity.entity_type' => $warehouseSearch->entity_type]);

        $query->andFilterWhere(['warehouse.id' => $warehouseSearch->id]);
        $query->andFilterWhere(['group_id' => $warehouseSearch->group_ids]);
        $query->andFilterWhere(['ILIKE', 'number', $warehouseSearch->number]);
        $query->andFilterWhere(['ILIKE', 'address', $warehouseSearch->address]);
        $query->andFilterWhere(['ILIKE', 'city', $warehouseSearch->city]);
        $query->andFilterWhere(['ILIKE', 'zip', $warehouseSearch->zip]);
        $query->andFilterWhere(['warehouse_entity.entity_id' => $warehouseSearch->entity_id]);
        $query->andFilterWhere(['warehouse.type_id' => $warehouseSearch->type_id]);

        return $query;
    }
}
