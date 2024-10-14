<?php

namespace modules\warehouse\controllers\actions\transaction;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseTransactionController;
use modules\warehouse\services\WarehouseTransactionService;
use modules\warehouse\searches\WarehouseTransactionSearch;

/**
 * @property WarehouseTransactionController $controller
 */
class WarehouseTransactionIndexAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseTransactionController $controller,
        private readonly WarehouseTransactionService $warehouseTransactionService,
        private readonly WarehouseTransactionSearch $warehouseTransactionSearch,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): string
    {
        $this->warehouseTransactionSearch->load($this->getRequest()->get());
        $this->warehouseTransactionSearch->current_warehouse_id = $this->controller->entity->id;

        $totals = $this->warehouseTransactionService->getArticleTotalsByWarehouseId($this->controller->entity->id);

        $dataProvider = $this->warehouseTransactionService->getActiveDataProvider($this->warehouseTransactionSearch);

        return $this->render('index', [
            'searchModel' => $this->warehouseTransactionSearch,
            'filterDatesModel' => null,
            'dataProvider' => $dataProvider,
            'totalAmount' => $totals['amount'],
            'totalReservedAmount' => $totals['reserved'],
        ]);
    }
}
