<?php

namespace modules\warehouse\controllers\actions\search;

use modules\commonTm\modules\EntityTypeSearchModel;
use modules\core\actions\WebAction;
use modules\warehouse\controllers\WarehouseSearchController;
use yii\web\Response;

class WarehouseParentTypeAjaxSearchAction extends WebAction
{
    public function __construct(
        string $id,
        WarehouseSearchController $controller,
        private readonly EntityTypeSearchModel $entityTypeSearchModel,
    ) {
        parent::__construct($id, $controller);
    }

    public function run(): array
    {
        $this->getResponse()->format = Response::FORMAT_JSON;

        $this->entityTypeSearchModel->search = $this->getRequest()->get('q');

        return [
            'results' => $this->controller->entityTypeProvider
                ->getListForSelect2Ajax($this->entityTypeSearchModel),
        ];
    }
}
