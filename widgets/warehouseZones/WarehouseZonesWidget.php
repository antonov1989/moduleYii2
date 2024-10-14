<?php

namespace modules\warehouse\widgets\warehouseZones;

use Closure;
use Exception;
use siot\general\helpers\{ArrayHelper, Html};
use Yii;
use yii\bootstrap5\Widget;

class WarehouseZonesWidget extends Widget
{
    public array $categories = [];
    public array $categoryOptions = [];
    public string|array|Closure|null $addUrl = null;
    public string|array|Closure|null $updateUrl = null;
    public string|array|Closure|null $deleteUrl = null;
    public string|array|Closure|null $actions = null;
    public string $labelKey = 'name';
    public bool $encodeLabel = true;
    public string $actionsTemplate = '{add}{update}{delete}';
    public string $deleteConfirmMessage = 'Are you sure you want to delete this group?';
    public string $emptyText = 'No results to display';
    public bool $canUpdateZone;
    public bool $canDeleteZone;
    public bool $canCreateZone;

    public function init(): void
    {
        parent::init();
        unset($this->options['id']);
    }

    /**
     * @throws Exception
     */
    public function run(): string
    {
        if (empty($this->categories)) {
            return $this->renderEmpty();
        }

        return $this->renderCategories($this->categories);
    }

    /**
     * @throws Exception
     */
    private function renderCategories(array $categories, array $options = []): string
    {
        $result = [];
        foreach ($categories as $category) {
            $result[] = $this->renderCategory($category);
        }
        $options = array_merge($options, $this->options);
        Html::addCssClass($options, 'list-group-categories list-group');

        return Html::tag('ul', implode($result), $options);
    }

    /**
     * @throws Exception
     */
    private function renderCategory(array $item): string
    {
        $label = ArrayHelper::getValue($item, $this->labelKey);

        if ($label === null) {
            return '';
        }
        $options = $this->categoryOptions;
        Html::addCssClass($options, 'list-group-item');
        $content = Html::beginTag('li', $options);
        $content .= Html::beginDiv(['class' => 'category-content d-flex justify-content-between align-items-center']);
        $content .= Html::span($this->encodeLabel ? Html::encode($label) : $label);
        $content .= Html::tag('div', $this->renderActions($item), ['class' => 'actions']);
        $content .= Html::endDiv();
        $content .= Html::endTag('li');

        if (!empty($item['children'])) {
            $content .= $this->renderCategories($item['children'], ['style' => 'padding-left:50px']);
        }

        return $content;
    }

    private function renderActions(array $item): string
    {
        return strtr($this->actionsTemplate, [
            '{delete}' => $this->renderDeleteButton($item),
            '{add}'    => $this->renderAddButton($item),
            '{update}' => $this->renderUpdateButton($item),
        ]);
    }

    private function renderAddButton(array $item): string
    {
        if ($this->addUrl === null) {
            return '';
        }

        $options = [
            'type' => 'success',
            'icon' => 'plus',
            'class' => 'me-5',
        ];

        if (!$this->canCreateZone) {
            $options = array_merge($options, [
                'disabled' => true,
                'class' => 'disabled',
                'aria-disabled' => 'true'
            ]);
        }

        return $this->renderActionButton(
            $this->prepareUrl($this->addUrl, $item),
            $options
        );
    }

    private function renderUpdateButton(array $item): string
    {
        if ($this->updateUrl === null) {
            return '';
        }

        $options = [
            'type' => 'warning',
            'icon' => 'edit'
        ];

        if (!$this->canUpdateZone) {
            $options = array_merge($options, [
                'disabled' => true,
                'class' => 'disabled',
                'aria-disabled' => 'true'
            ]);
        }

        return $this->renderActionButton(
            $this->prepareUrl($this->updateUrl, $item),
            $options
        );
    }

    private function renderDeleteButton(array $item): string
    {
        if ($this->deleteUrl === null) {
            return '';
        }
        $options = [
            'type' => 'danger',
            'icon' => 'trash',
            'data-method' => 'post',
            'data-confirm' => Yii::t('app', $this->deleteConfirmMessage)
        ];

        $isHasChildren = array_key_exists('children', $item);

        if (!$this->canDeleteZone || $isHasChildren) {
            $options = array_merge($options, [
                'disabled' => true,
                'class' => 'disabled',
                'aria-disabled' => 'true'
            ]);
        }

        return $this->renderActionButton($this->prepareUrl($this->deleteUrl, $item), $options);
    }

    private function renderActionButton(string|array $url, array $options = []): string
    {
        $type = ArrayHelper::remove($options, 'type');

        if ($type === null) {
            return '';
        }
        Html::addCssClass($options, "button is-light is-small is-$type mx-1");

        return Html::a('', $url, $options);
    }

    private function renderEmpty(): string
    {
        return Html::tag('p', Yii::t('app', $this->emptyText));
    }

    private function prepareUrl(string|array|Closure $url, array $item): string|array
    {
        if ($url instanceof Closure) {
            $url = call_user_func($url, $item);
        }

        return $url;
    }
}
