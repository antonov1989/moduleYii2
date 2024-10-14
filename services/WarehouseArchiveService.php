<?php

namespace modules\warehouse\services;

use modules\core\exceptions\ArchiveRestrictionException;
use modules\core\exceptions\EntityNotFoundException;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\SaveException;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\repositories\WarehouseRepository;

class WarehouseArchiveService
{
    public function __construct(
        private readonly WarehouseRepository $warehouseRepository,
        private readonly WarehouseArchiveCheckRestrictionsService $warehouseArchiveRestrictionsService
    ) {
    }

    /**
     * @throws ArchiveRestrictionException
     * @throws EntityValidateException
     * @throws SaveException
     * @throws EntityNotFoundException
     */
    public function archive(string $warehouseId): void
    {
        $warehouse = $this->getWarehouse($warehouseId);

        if (!$warehouse) {
            throw new EntityNotFoundException('The warehouse is not found.');
        }

        $this->setArchived($warehouse);
        $this->validateEntity($warehouse);
        $this->saveEntity($warehouse);
    }

    /**
     * @throws ArchiveRestrictionException
     */
    public function getWarehouse(string $warehouseId): ?Warehouse
    {
        $this->isAllowedToBeArchived($warehouseId);

        return $this->warehouseRepository->findById($warehouseId);
    }

    /**
     * @throws ArchiveRestrictionException
     */
    private function isAllowedToBeArchived(string $warehouseId): void
    {
        $isAllowed = $this->warehouseArchiveRestrictionsService->isAllowedToBeArchived($warehouseId);

        if (!$isAllowed) {
            throw new ArchiveRestrictionException();
        }
    }

    private function setArchived(Warehouse $warehouse): void
    {
        $warehouse->status = Warehouse::STATUS_DELETED;
        $warehouse->deleted_at = time();
    }

    /**
     * @throws EntityValidateException
     */
    private function validateEntity(Warehouse $warehouse): void
    {
        $isValid = $warehouse->validate();

        if (!$isValid) {
            $exception = new EntityValidateException();
            $exception->setData($warehouse->getErrors());

            throw $exception;
        }
    }

    /**
     * @throws SaveException
     */
    private function saveEntity(Warehouse $warehouse): void
    {
        $isSaved = $warehouse->save();

        if (!$isSaved) {
            $exception = new SaveException();
            $exception->setData($warehouse->getErrors());

            throw $exception;
        }
    }
}
