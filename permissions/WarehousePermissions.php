<?php

namespace modules\warehouse\permissions;

use Iwms\Core\File\Permissions\FilePermissions;
use Iwms\Core\Note\Permissions\NotePermissions;
use modules\core\permissions\GroupPermissions;
use modules\core\permissions\ArchivePermissions;
use modules\notifications\permissions\NotificationsPermissions;
use modules\workingScheme\permissions\WorkingSchemePermissions;

class WarehousePermissions extends GroupPermissions
{
    public function getAll(): array
    {
        return [
            $this->read()->get(),
            $this->create()->get(),
            $this->update()->get(),
            $this->delete()->get(),
        ];
    }

    public function read(): self
    {
        $this->setPermission('warehouses_read');

        return $this;
    }

    public function create(): self
    {
        $this->setPermission('warehouses_create');

        return $this;
    }

    public function update(): self
    {
        $this->setPermission('warehouses_update');

        return $this;
    }

    public function delete(): self
    {
        $this->setPermission('warehouses_delete');

        return $this;
    }

    public function archive(): ArchivePermissions
    {
        return new ArchivePermissions('warehouses');
    }

    public function notifications(): NotificationsPermissions
    {
        return new NotificationsPermissions('warehouses');
    }

    public function workingScheme(): WorkingSchemePermissions
    {
        return new WorkingSchemePermissions('warehouse');
    }

    public function note(): NotePermissions
    {
        return new NotePermissions('warehouse');
    }

    public function file(): FilePermissions
    {
        return new FilePermissions('warehouse');
    }

    public function zoneRead(): self
    {
        $this->setPermission('warehouse_zones_read');

        return $this;
    }

    public function zoneCreate(): self
    {
        $this->setPermission('warehouse_zones_create');

        return $this;
    }

    public function zoneUpdate(): self
    {
        $this->setPermission('warehouse_zones_update');

        return $this;
    }

    public function zoneDelete(): self
    {
        $this->setPermission('warehouse_zones_delete');

        return $this;
    }

    public function transactionRead(): self
    {
        $this->setPermission('warehouse_transactions_read');

        return $this;
    }
}
