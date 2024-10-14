<?php

use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\core\enums\ModuleTypeEnum;
use modules\core\permissions\Permissions;
use modules\warehouse\WarehouseModule;
use modules\warehouse\entities\Warehouse;
use siot\general\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];
$entity = $this->params['entity'];
$isAllowedToBeArchived = $this->params['isAllowedToBeArchived'] ?? true;

if ($entity->status === Warehouse::STATUS_DELETED) {
    $archiveBtn = [
        'label' => Html::span(
            Yii::t('app', 'Restore warehouse'),
            ['class' => 'd-none d-md-inline']
        ),
        'icon' => 'trash-undo',
        'class' => 'button-modernize button-success me-4',
        'data' => [
            'bs-toggle' => 'modal',
            'bs-target' => '#createConfirmRestore',
        ],
        'access' => Permissions::getApp()->warehouse()->archive()->restore()->get(),
    ];
}
else {
    $archiveBtn = [
        'label' => Html::span(
            Yii::t('app', 'Archive warehouse'),
            ['class' => 'd-none d-md-inline']
        ),
        'icon' => 'trash',
        'class' => 'button-modernize button-cancel me-4'. (!$isAllowedToBeArchived ? ' disabled' : ''),
        'data' => [
            'bs-toggle' => 'modal',
            'bs-target' => '#createConfirmArchive',
        ],
        'access' => Permissions::getApp()->warehouse()->archive()->create()->get(),
    ];
}

?>

<?php
$items = [
    'information' => [
        'label' => Yii::t('app', 'Information'),
        'customUrl' => $moduleRouter->to('control.info', $entity->id),
        'icon' => 'info',
        'access' => Permissions::getApp()->warehouse()->update()->get(),
        'data-testid' => 'testInformationTab'
    ],
    'transaction' => [
        'label' => Yii::t('app', 'Report'),
        'customUrl' => $moduleRouter->to('transaction.index',  $entity->id),
        'icon' => 'exchange-alt',
        'access' => Permissions::getApp()->warehouse()->transactionRead()->get(),
        'data-testid' => 'testReportTab'
    ],
    'zone' => [
        'label' => Yii::t('app', 'Zones'),
        'customUrl' => $moduleRouter->to('zone.index',  $entity->id),
        'icon' => 'list',
        'access' => Permissions::getApp()->warehouse()->zoneRead()->get(),
        'data-testid' => 'testZonesTab'
    ],
];
$linksToNestedModules = [
    ModuleTypeEnum::workingScheme->value => [
        'label' => Yii::t('app', 'Working scheme'),
        'customUrl' => $moduleRouter->to('working-scheme.control.index',  $entity->id),
        'icon' => 'list-alt',
        'access' => Permissions::getApp()->warehouse()->workingScheme()->read()->get(),
        'data-testid' => 'testWorkingSchemeTab'
    ],
    ModuleTypeEnum::note->value => [
        'label' => Yii::t('app', 'Notes'),
        'customUrl' => $moduleRouter->to('note.control.index', $entity->id),
        'icon' => 'sticky-note',
        'access' => Permissions::getApp()->warehouse()->note()->read()->get(),
        'data-testid' => 'testNotesTab'
    ],
    ModuleTypeEnum::file->value => [
        'label' => Yii::t('app', 'Files'),
        'url' => $moduleRouter->to('file.control.index', $entity->id),
        'icon' => 'file',
        'access' => Permissions::getApp()->warehouse()->file()->read()->get(),
        'data-testid' => 'testFilesTab'
    ],
];
$nestedModules = $this->context->module->modules;
foreach ($linksToNestedModules as $moduleName => $link) {
    if (!array_key_exists($moduleName, $nestedModules)) {
        unset($linksToNestedModules[$moduleName]);
    }
}

$items = array_merge($items, $linksToNestedModules);

?>

<?= $this->render('@coreLayouts/module-layout-template', [
    'content' => $content,
    'name' => $entity->name,
    'menu' => [
        'moduleClass' => WarehouseModule::class,
        'items' => $items,
    ],
    'actionButtons' => [
        $archiveBtn,
    ],
]); ?>

<?php if ($entity->status === Warehouse::STATUS_DELETED
    && Permissions::getApp()->warehouse()->archive()->restore()->get()): ?>
    <?= $this->render('/common/modal/confirmRestore'); ?>
<?php endif; ?>

<?php if ($entity->status !== Warehouse::STATUS_DELETED
    && Permissions::getApp()->warehouse()->archive()->create()->get()): ?>
    <?= $this->render('/common/modal/confirmArchive'); ?>
<?php endif; ?>

