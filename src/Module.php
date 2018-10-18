<?php

namespace ofixone\content;

use ofixone\admin\interfaces\ModuleInterface;
use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module implements ModuleInterface
{
    public $controllerNamespace = "ofixone\content\controllers";
    public $defaultRoute = 'list';

    public $type;
    public $names = [
        self::NAME_ONE => 'Контент',
        self::NAME_COUPLE => 'Элемента контента',
        self::NAME_MANY => 'Элементов контета'
    ];
    public $menuItem = [];

    const TYPE_SINGLE = 'single';
    const TYPE_MULTIPLE = 'multiple';
    const NAME_ONE = 0;
    const NAME_COUPLE = 1;
    const NAME_MANY = 2;

    public function addRules(): array
    {
        return [
            [
                'pattern' => '<module>/' . $this->id . '/<action>',
                'route' => '<module>/' . $this->id . '/module/' . '<action>'
            ]
        ];
    }

    public function addMenuItem(): array
    {
        return [
            ArrayHelper::merge([
                'label' => $this->type == self::TYPE_MULTIPLE ? $this->names[self::NAME_MANY] : $this->names[self::NAME_ONE],
                'icon' => 'folder',
                'url' => [
                    "/" . $this->getUniqueId() . "/" . $this->defaultRoute
                ],
                'active' => \Yii::$app->controller->module->id == $this->id
            ], $this->menuItem)
        ];
    }
}