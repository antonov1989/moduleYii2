<?php

namespace modules\warehouse\controllers\actions\search;

enum WarehouseSearchActionIds: string
{
    case ajaxSearch = 'ajax-search';
    case parentTypeAjaxSearch = 'parent-type-ajax-search';
}
