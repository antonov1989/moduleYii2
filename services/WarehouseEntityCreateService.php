<?php

namespace modules\warehouse\services;

use modules\core\exceptions\EntityValidateException;
use modules\core\exceptions\SaveException;
use modules\warehouse\entities\WarehouseEntity;
use modules\warehouse\factories\WarehouseEntityFactory;
use modules\warehouse\models\WarehouseEntityBaseModel;

class WarehouseEntityCreateService
{
    private WarehouseEntity|null $entity;

    public function __construct(
        private readonly WarehouseEntityFactory $warehouseEntityFactory,
    ) {
    }

    /**
     * @throws EntityValidateException
     * @throws SaveException
     */
    public function create(WarehouseEntityBaseModel $entityCreateModel): void
    {
        $this->entity = $this->warehouseEntityFactory->instantiate();
        $this->fillEntity($entityCreateModel);
        $this->validateEntity();
        $this->saveEntity();
    }

    private function fillEntity(WarehouseEntityBaseModel $entityCreateModel): void
    {
        $this->entity->warehouse_id = $entityCreateModel->warehouse_id;
        $this->entity->entity_id = $entityCreateModel->entity_id;
        $this->entity->entity_type = $entityCreateModel->entity_type;
    }

    /**
     * @throws EntityValidateException
     */
    private function validateEntity(): void
    {
        $isValid = $this->entity->validate();

        if (! $isValid) {
            $exception = new EntityValidateException();
            $exception->setData($this->entity->getErrors());

            throw $exception;
        }
    }

    /**
     * @throws SaveException
     */
    private function saveEntity(): void
    {
        $isSaved = $this->entity->save();

        if (! $isSaved) {
            $exception = new SaveException();
            $exception->setData($this->entity->getErrors());

            throw $exception;
        }
    }
}
