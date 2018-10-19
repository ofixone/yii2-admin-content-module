<?php

namespace ofixone\content;

use ofixone\admin\interfaces\ModuleInterface;
use ofixone\content\assets\ModuleAsset;
use ofixone\content\models\FilterModel;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @property boolean $isMultipleType
 * @property boolean $isSingleType
 * @package ofixone\content
 */
class Module extends \yii\base\Module implements ModuleInterface
{
    public $controllerNamespace = "ofixone\content\controllers";
    public $defaultRoute = 'list';

    public $type = self::TYPE_MULTIPLE;
    public $names = [
        self::NAME_ONE => 'Контент',
        self::NAME_COUPLE => 'Элемента контента',
        self::NAME_MANY => 'Элементов контета'
    ];
    public $menuItem = [];
    public $model;
    public $filterModel;
    public $disableCreate = false;
    public $disableDelete = false;

    const TYPE_SINGLE = 'single';
    const TYPE_MULTIPLE = 'multiple';
    const NAME_ONE = 0;
    const NAME_COUPLE = 1;
    const NAME_MANY = 2;

    public function init()
    {
        parent::init();
        ModuleAsset::register(\Yii::$app->view);
        $this->checkModels();
    }

    public function checkModels()
    {
        if(empty($this->model)) {
            throw new InvalidConfigException('Необходимо задать модель '. ActiveRecord::class . ' для ' .
                ' контент-модуля '. $this->names[self::NAME_ONE]
            );
        }
        if(empty($this->filterModel) && $this->isMultipleType) {
            throw new InvalidConfigException('Необходимо задать модель фильтрации '. FilterModel::class .
                ' для контент-модуля '. $this->names[self::NAME_ONE]
            );
        } else if(!empty($this->filterModel) && $this->isMultipleType) {
            $test = new $this->filterModel;
            if(!$test instanceof FilterModel) {
                throw new InvalidConfigException('Модель фильтрации '. $this->filterModel . ' должна наследоваться' .
                    ' от ' . FilterModel::class);
            }
        }
    }

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
                'label' => $this->type == self::TYPE_MULTIPLE ? $this->names[self::NAME_COUPLE] : $this->names[self::NAME_ONE],
                'icon' => 'folder',
                'url' => [
                    "/" . $this->getUniqueId() . "/" . $this->defaultRoute
                ],
                'active' => \Yii::$app->controller->module->id == $this->id
            ], $this->menuItem)
        ];
    }

    public function getIsMultipleType()
    {
        return $this->type == self::TYPE_MULTIPLE;
    }

    public function getIsSingleType()
    {
        return $this->type == self::TYPE_SINGLE;
    }

}