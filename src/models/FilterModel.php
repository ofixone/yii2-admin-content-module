<?php

namespace ofixone\content\models;

use kartik\checkbox\CheckboxX;
use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use ofixone\filekit\Upload;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\redactor\widgets\Redactor;
use yii\web\NotFoundHttpException;

abstract class FilterModel extends Model
{
    abstract public function query(): ActiveQuery;

    public function getDefaultSearchAttributes()
    {
        return [
            'like' => function (ActiveQuery $query, string $attribute) {
                return !empty($this->{$attribute}) ? $query->andWhere(['like', $attribute, $this->{$attribute}]) : $query;
            },
            '=' => function (ActiveQuery $query, string $attribute) {
                return !empty($this->{$attribute}) ? $query->andWhere([$attribute => $this->{$attribute}]) : $query;
            },
        ];
    }

    public function getSearchAttributes()
    {
        return [

        ];
    }

    public function getGridColumns()
    {
        return [

        ];
    }

    public function getDataProvider(): ActiveDataProvider
    {
        $query = $this->query();
        foreach ($this->getSearchAttributes() as $attribute => $search) {
            if (is_string($search)) {
                if (!empty($this->getDefaultSearchAttributes()[$search])) {
                    $query = call_user_func($this->getDefaultSearchAttributes()[$search], $query, $attribute);
                } else {
                    throw new InvalidConfigException('Нет предустановленного метода фильтрации ' . $search .
                        ". Имеющиеся предустановленые методы " . implode(array_keys(
                            $this->getDefaultSearchAttributes()
                        ))
                    );
                }
            } else if (is_array($search)) {
                $query = !empty($this->{$attribute}) ? $query->andWhere($search) : $query;
            } else if ($search instanceof \Closure) {
                $query = call_user_func($search, $query);
            } else {
                throw new NotFoundHttpException('Неправильный атрибут фильтрации ' . $search);
            }
        }
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function getAttributeWidgets()
    {
        return [
            'fields' => [

            ],
            'options' => [
                'handleNotInFields' => true
            ]
        ];
    }

    private function attributeWidgets(ActiveForm $form, ActiveRecord $model)
    {
        $html = '';
        $tabs = [];
        $attributeWidgets = ArrayHelper::merge([
            'fields' => [],
            'options' => [
                'tabs' => false
            ]
        ], $this->getAttributeWidgets()) ;
        if(!empty($attributeWidgets['fields'])) {
            foreach($attributeWidgets['fields'] as $key => $value) {
                if($attributeWidgets['options']['tabs'] === true && is_array($value)) {
                    foreach($value as $key2 => $value2) {
                        if(empty($tabs[$key])) {
                            $tabs[$key] = $this->printWidget($key2, $value2, $form, $model);
                        } else {
                            $tabs[$key] .= $this->printWidget($key2, $value2, $form, $model);
                        }
                    }
                } else if($attributeWidgets['options']['tabs'] === true) {
                    throw new InvalidConfigException('Массив fields у метода ' . __METHOD__ . ' при включенной ' .
                        'табуляции должен массивом массивов полей'
                    );
                } else if($attributeWidgets['options']['tabs'] !== true) {
                    $html .= $this->printWidget($key, $value, $form, $model);
                }
            }
        }
        if($attributeWidgets['options']['tabs'] === true && !empty($tabs)) {
            $items = [];
            foreach($tabs as $key => $item) {
                $items[] = [
                    'label' => $key,
                    'content' => $item
                ];
            }
            $html .= Html::tag('div', Tabs::widget([
                'items' => $items
            ]), [
                'class' => 'nav-tabs-custom'
            ]);
            return $html;
        } else {
            return Html::tag('div', Html::tag('div', $html, [
                'class' => 'box-body'
            ]), [
                'class' => ['box', 'box-default']
            ]);
        }
    }

    private function printWidget($attribute, $config, ActiveForm $form, ActiveRecord $model)
    {
        if(is_array($config)) {
            $widget = ArrayHelper::remove($config, 0);
            switch($widget) {
                case "input":
                    return $form->field($model, $attribute)->input(ArrayHelper::remove($config, 1),
                        !empty($config) ? $config : []
                    );
                    break;
                case "textarea":
                    return $form->field($model, $attribute)->textarea(
                        !empty($config) ? $config : []
                    );
                    break;
                case "select":
                    return $form->field($model, $attribute)->widget(Select2::class, ArrayHelper::merge(
                        [
                            'pluginOptions' => [
                                'allowClear' => true,
                                'placeholder' => 'Выберите'
                            ]
                        ],
                        $config
                    ));
                    break;
                case 'date':
                    return $form->field($model, $attribute)->widget(DatePicker::class, ArrayHelper::merge(
                        [
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ],
                        $config
                    ));
                    break;
                case 'image':
                    return $form->field($model, $attribute)->widget(Upload::class, ArrayHelper::merge(
                        [
                            'url' => ['upload'],
                            'cropUrl' => ['crop'],
                            'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png|svg)$/i'),
                        ],
                        $config
                    ));
                    break;
                case 'file':
                    return $form->field($model, $attribute)->widget(Upload::class, ArrayHelper::merge(
                        [
                            'url' => ['upload'],
                            'cropUrl' => ['crop']
                        ],
                        $config
                    ));
                    break;
                case 'redactor':
                    return $form->field($model, $attribute)->widget(Redactor::class, !empty($config) ? $config : []);
                    break;
                case 'inputRedactor':
                    return $form->field($model, $attribute)->widget(Redactor::class, ArrayHelper::merge(
                        [
                            'clientOptions' => [
                                'uploadImage' => false,
                                'uploadFile' => false,
                                'buttons' => ['html', 'bold', 'italic', 'deleted', 'link']
                            ]
                        ],
                        $config
                    ));
                    break;
                case 'checkbox':
                    return $form->field($model, $attribute)->widget(CheckboxX::class, [
                        'pluginOptions' => [
                            'threeState' => false
                        ],
                        'autoLabel' => true
                    ])->label(false);
                    break;
                default:
                    return $form->field($model, $attribute)->widget(ArrayHelper::remove($config, 0), $config);
                    break;
            }
        } else if(is_bool($config) && $config === false) {
            return '';
        }
        return '';
    }

    public function getForm(ActiveForm $form, ActiveRecord $model): string
    {
        return $this->attributeWidgets($form, $model);
    }
}