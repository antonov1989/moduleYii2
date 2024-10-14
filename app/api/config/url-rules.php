<?php

use modules\commonTm\helpers\EntityHelper;

$entityIdPattern = EntityHelper::ENTITY_UUID_PATTERN;

return [
    "GET,OPTIONS <entityType>/<entityId:$entityIdPattern>/warehouses" => '<entityType>/warehouse/warehouse/index',
    "POST <entityType>/<entityId:$entityIdPattern>/warehouses" => '<entityType>/warehouse/warehouse/create',
    "PUT <entityType>/warehouses/<id:$entityIdPattern>" => '<entityType>/warehouse/warehouse/update',
    "DELETE <entityType>/warehouses/<id:$entityIdPattern>" => '<entityType>/warehouse/warehouse/delete',
    "PATCH <entityType>/warehouses/<id:$entityIdPattern>" => '<entityType>/warehouse/warehouse/restore',
];
