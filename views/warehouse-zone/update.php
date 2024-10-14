<?php

use frontend\components\web\View;
use frontend\helpers\Url;
use Iwms\Core\General\Components\Router\ModuleRouter;
use modules\warehouse\entities\WarehouseZone;
use siot\general\helpers\Html;
use siot\general\widgets\Card;

/**
 * @var View $this
 * @var WarehouseZone $model
 * @var array $zonesMap
 */

$this->title = Yii::t('app', 'Zones');
$this->fullSize = true;

/** @var ModuleRouter $moduleRouter */
$moduleRouter = $this->params['moduleRouter'];
$entity = $this->params['entity'];

?>

<?php Card::begin([
    'title' => Yii::t('app', "Update zone '$model->name'"),
    'controls' => function () use ($moduleRouter, $entity): string {
        return Html::a(
            Yii::t('app', 'Back to list'),
            $moduleRouter->to('zone.index', $entity->id),
            ['class' => 'button is-gray', 'icon' => 'angle-left']
        );
    },
]); ?>
    <?= $this->render('_form', [
        'model' => $model,
        'zonesMap' => $zonesMap,
    ]); ?>
<?php Card::end(); ?>
