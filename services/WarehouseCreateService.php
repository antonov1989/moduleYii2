<?php

namespace modules\warehouse\services;

use Exception;
use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\SaveException;
use modules\warehouse\entities\Warehouse;
use modules\warehouse\entities\WarehouseEntity;
use modules\warehouse\models\WarehouseCreateModel;
use Yii;

class WarehouseCreateService
{
    public function __construct(
        private readonly Warehouse $warehouse,
    ) {
    }

    /**
     * @throws EntityValidateException
     * @throws SaveException
     * @throws Exception
     */
    public function create(WarehouseCreateModel $warehouseCreateModel): Warehouse
    {
        $this->fillEntity($warehouseCreateModel);
        $this->processTransaction($warehouseCreateModel);

        return $this->warehouse;
    }

    private function fillEntity(WarehouseCreateModel $warehouseCreateModel): void
    {
        $this->warehouse->type_id = $warehouseCreateModel->type_id;
        $this->warehouse->name = $warehouseCreateModel->name;
        $this->warehouse->email = $warehouseCreateModel->email;
        $this->warehouse->number = $warehouseCreateModel->number;
        $this->warehouse->address = $warehouseCreateModel->address;
        $this->warehouse->zip = $warehouseCreateModel->zip;
        $this->warehouse->city = $warehouseCreateModel->city;
        $this->warehouse->group_id = $warehouseCreateModel->group_id;
    }

    /**
     * @throws EntityValidateException
     * @throws SaveException
     * @throws Exception
     */
    private function processTransaction(WarehouseCreateModel $warehouseCreateModel): void
    {
        $dbTransaction = Yii::$app->getDb()->beginTransaction();

        try {
            $this->validateEntity();
            $this->saveEntity();

            $warehouseEntity = new WarehouseEntity();
            $warehouseEntity->entity_type = $warehouseCreateModel->entity_type;
            $warehouseEntity->entity_id = $warehouseCreateModel->entity_id;
            $warehouseEntity->warehouse_id = $this->warehouse->id;
            if (!$warehouseEntity->save()) {
                $exception = new SaveException();
                $exception->setData($warehouseEntity->getErrors());

                throw $exception;
            }

            $dbTransaction->commit();
        } catch (Exception $exception) {
            $dbTransaction->rollBack();

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
