<?php

namespace modules\warehouse\controllers\actions\search;

use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseSearchController;
use modules\warehouse\repositories\WarehouseRepository;
use modules\warehouse\searches\WarehouseSearch;
use yii\web\Response;

class WarehouseAjaxSearchAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseSearchController $controller,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly WarehouseSearch $warehouseSearch
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): array
    {
        $this->getResponse()->format = Response::FORMAT_JSON;

        $this->warehouseSearch->search = $this->getRequest()->get('q');
        $this->warehouseSearch->status = $this->getRequest()->get('status');
        $this->warehouseSearch->entity_type = $this->controller->warehouseType;

        return [
            'results' => $this->warehouseRepository->getForSelect2Ajax($this->warehouseSearch),
        ];
    }
}
