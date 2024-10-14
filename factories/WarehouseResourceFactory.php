<?php

namespace modules\warehouse\factories;

use modules\warehouse\resources\WarehouseResource;
use modules\warehouse\entities\Warehouse;
use modules\core\factories\ResourceFactory;
use modules\core\resources\collection\ResourceCollection;
use modules\core\resources\dto\MetaDto;

class WarehouseResourceFactory
{
    public function __construct(
        private readonly ResourceFactory $resourceFactory
    ) {
    }

    public function createWarehouseResource(Warehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource(
            id: $warehouse->id,
            entity_id: $warehouse->warehouseEntity->entity_id,
            entity_type: $warehouse->warehouseEntity->entity_type,
            type: $warehouse->type_id,
            name: $warehouse->name,
            description: '', // TODO: field doesn't exist in the table. Need in response for portal
            status: $warehouse->status,
            address: $warehouse->address,
            zip: $warehouse->zip,
            number: $warehouse->number,
            city: $warehouse->city,
            sum_price: null, // TODO: field doesn't exist in the table. Maybe it is economic part
            parent_id: null, // TODO: field doesn't exist in the table. Need in response for portal
            antenna_status: null, // TODO: field doesn't exist in the table. Need in response for portal
            unique_articles_count: null, // TODO: field doesn't exist in the table. Need in response for portal
            project: null, // TODO: field doesn't exist in the table. Need in response for portal
        );
    }

    /**
     * @param Warehouse[] $warehouses
     */
    public function createWarehouseResourceCollection(array $warehouses, MetaDto $metaDto): ResourceCollection
    {
        $resources = [];
        foreach ($warehouses as $warehouse) {
            $resources[] = $this->createWarehouseResource($warehouse);
        }

        return $this->resourceFactory->createResourceCollection($resources, $metaDto);
    }
}
