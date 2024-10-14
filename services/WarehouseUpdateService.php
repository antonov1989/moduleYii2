<?php

namespace modules\warehouse\services;

use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\SaveException;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\models\WarehouseUpdateModel;
use modules\warehouse\repositories\WarehouseRepository;

class WarehouseUpdateService
{
    private ?Warehouse $warehouse = null;

    public function __construct(
        private readonly WarehouseRepository $warehouseRepository
    ) {
    }

    public function update(string|int $entityId, WarehouseUpdateModel $warehouseUpdateModel): Warehouse
    {
        $this->warehouse = $this->warehouseRepository->findById($entityId);

        $this->fillEntity($warehouseUpdateModel);
        $this->processTransaction($warehouseUpdateModel);

        return $this->warehouse;
    }

    private function fillEntity(WarehouseUpdateModel $warehouseUpdateModel): void
    {
        $this->warehouse->email = $warehouseUpdateModel->email;
        $this->warehouse->type_id = $warehouseUpdateModel->type_id;
        $this->warehouse->name = $warehouseUpdateModel->name;
        $this->warehouse->number = $warehouseUpdateModel->number;
        $this->warehouse->address = $warehouseUpdateModel->address;
        $this->warehouse->zip = $warehouseUpdateModel->zip;
        $this->warehouse->city = $warehouseUpdateModel->city;
        $this->warehouse->group_id = $warehouseUpdateModel->group_id;
    }

    private function processTransaction(WarehouseUpdateModel $warehouseUpdateModel): void
    {
        try {
            $this->validateEntity();
            $this->saveEntity();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @throws EntityValidateException
     */
    private function validateEntity(): void
    {
        $isValid = $this->warehouse->validate();

        if (! $isValid) {
            $exception = new EntityValidateException();
            $exception->setData($this->warehouse->getErrors());

            throw $exception;
        }
    }

    /**
     * @throws SaveException
     */
    private function saveEntity(): void
    {
        $isSaved = $this->warehouse->save();

        if (! $isSaved) {
            $exception = new SaveException();
            $exception->setData($this->warehouse->getErrors());

            throw $exception;
        }
    }
}
