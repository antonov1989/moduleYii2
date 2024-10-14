<?php

namespace modules\warehouse\services;

use modules\core\exceptions\EntityNotFoundException;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\RestoreException;
use modules\core\exceptions\SaveException;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\repositories\WarehouseRepository;

class WarehouseRestoreService
{
    public function __construct(
        private readonly WarehouseRepository $warehouseRepository,
    ) {
    }

    /**
     * @throws EntityValidateException
     * @throws SaveException
     * @throws RestoreException
     * @throws EntityNotFoundException
     */
    public function restore(string $warehouseId): Warehouse
    {
        $warehouse = $this->warehouseRepository->findById($warehouseId);

        if (!$warehouse) {
            throw new EntityNotFoundException('The warehouse type is not found.');
        }

        $this->isAllowedToBeRestored($warehouse);

        $this->setRestored($warehouse);
        $this->validateEntity($warehouse);
        $this->saveEntity($warehouse);

        return $warehouse;
    }

    /**
     * @throws RestoreException
     */
    private function isAllowedToBeRestored(Warehouse $warehouse): void
    {
        if (!$warehouse->isDeleted()) {
            throw new RestoreException();
        }
    }

    private function setRestored(Warehouse $warehouse): void
    {
        $warehouse->makeActive();
        $warehouse->deleted_at = null;
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
