<?php

return [
    'index' => '/',
    'create' => 'create',
    'validate-create' => 'validate-create',
    'archive' => 'archive',
    'ajax-search' => 'ajax-search',

    'control' => [
        'info' => 'control/info',
        'update' => 'control/update',
        'validate-update' => 'control/validate-update',
    ],

    'archive-control' => [
        'create' => 'archive-control/create-archive',
        'restore' => 'archive-control/restore',
    ],

    'search' => [
        'ajax-search' => 'search/ajax-search',
        'parent-type-ajax-search' => 'search/parent-type-ajax-search',
    ],

    'note' => [
        'control' => [
            'index' => 'note/control/index',
        ],
    ],

    'file' => [
        'control' => [
            'index' => 'file/control/index',
        ],
    ],

    'working-scheme' => [
        'control' => [
            'index' => 'working-scheme/control/index',
        ],
    ],

    'zone' => [
        'index' => 'zone/index',
        'create' => 'zone/create',
        'update' => 'zone/update',
        'delete' => 'zone/delete',
    ],

    'transaction' => [
        'index' => 'transaction/index',
    ],
];
