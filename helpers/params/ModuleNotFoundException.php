<?php

namespace modules\warehouse\helpers\params;

class ModuleNotFoundException extends \Exception
{
    public function __construct(string $moduleName = 'Warehouse')
    {
        $message = "Module [$moduleName] not found";

        parent::__construct($message, 500);
    }
}
