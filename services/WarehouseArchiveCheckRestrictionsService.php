<?php

namespace modules\warehouse\services;

use modules\article\repositories\ArticleTransactionRepository;
use modules\project\repositories\ProjectWarehouseRepository;

class WarehouseArchiveCheckRestrictionsService
{
    public function __construct(
        private readonly ArticleTransactionRepository $articleTransactionRepository,
        private readonly ProjectWarehouseRepository $projectWarehouseRepository,
    ) {
    }

    public function isAllowedToBeArchived(string $warehouseId): bool
    {
        if ($this->projectWarehouseRepository->getActiveProjectCount($warehouseId)) {
            return false;
        }

        if ($this->articleTransactionRepository->getZeroAmountCount($warehouseId)) {
            return false;
        }

        return true;
    }
}
