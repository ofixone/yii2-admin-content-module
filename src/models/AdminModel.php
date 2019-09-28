<?php

namespace ofixone\content\models;

use kartik\checkbox\CheckboxX;
use kartik\date\DatePicker;
use kartik\form\ActiveField;
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

abstract class AdminModel extends Model
{
    const FIELD_INPUT = 'input';
    const FIELD_TEXTAREA = 'textarea';
    const FIELD_SELECT = 'select';
    const FIELD_DATE = 'date';
    const FIELD_IMAGE = 'image';
    const FIELD_IMAGES = 'images';
    const FIELD_FILE = 'file';
    const FIELD_REDACTOR = 'redactor';
    const FIELD_SMALL_REDACTOR = 'smallRedactor';
    const FIELD_CHECKBOX = 'checkbox';

    public $model;

    abstract public function query(): ActiveQuery;

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

    public function getAttributeWidgets()
    {
        return [
            'fields' => [

            ],
            'options' => [

            ]
        ];
    }

    public function getAttributeWidgetsTypes()
    {
        return [

        ];
    }

    public function getDefaultSearchAttributes()
    {
        return [
            'like' => function (ActiveQuery $query, string $attribute) {
                return !empty($this->{$attribute}) ? $query->andWhere([
                    'like', $attribute, $this->{$attribute}
                ]) : $query;
            },
            '=' => function (ActiveQuery $query, string $attribute) {
                return !empty($this->{$attribute}) ? $query->andWhere([$attribute => $this->{$attribute}]) : $query;
            },
        ];
    }

    public function getDefaultAttributeWidgetsTypes()
    {
        return [
            self::FIELD_INPUT => function ($attribute, $config, $containerConfig,
                                           ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)
                    ->input(ArrayHelper::remove($config, 1),
                        !empty($config) ? $config : []
                    );
            },
            self::FIELD_TEXTAREA => function ($attribute, $config, $containerConfig,
                                              ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->textarea(
                    !empty($config) ? $config : []
                );
            },
            self::FIELD_SELECT => function ($attribute, $config, $containerConfig,
                                            ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->widget(Select2::class,
                    ArrayHelper::merge(
                        [
                            'pluginOptions' => [
                                'allowClear' => true,
                                'placeholder' => 'Выберите'
                            ]
                        ],
                        $config
                    )
                );
            },
            self::FIELD_DATE => function ($attribute, $config, $containerConfig,
                                          ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->widget(DatePicker::class,
                    ArrayHelper::merge(
                        [
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ]
                        ],
                        $config
                    )
                );
            },
            self::FIELD_IMAGE => function ($attribute, $config, $containerConfig,
                                           ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->widget(Upload::class,
                    ArrayHelper::merge(
                        [
                            'url' => ['upload'],
                            'cropUrl' => ['crop'],
                            'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png|svg)$/i'),
                        ],
                        $config
                    )
                );
            },
            self::FIELD_IMAGES => function ($attribute, $config, $containerConfig,
                                            ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->widget(Upload::class,
                    ArrayHelper::merge(
                        [
                            'multiple' => true,
                            'sortable' => true,
                            'maxNumberOfFiles' => ArrayHelper::remove($config, 'max', 10),
                            'url' => ['upload'],
                            'cropUrl' => ['crop'],
                            'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png|svg)$/i'),
                        ],
                        $config
                    )
                );
            },
            self::FIELD_FILE => function ($attribute, $config, $containerConfig,
                                          ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->widget(Upload::class,
                    ArrayHelper::merge(
                        [
                            'url' => ['upload'],
                            'cropUrl' => ['crop'],
                        ],
                        $config
                    )
                );
            },
            self::FIELD_REDACTOR => function ($attribute, $config, $containerConfig,
                                              ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)
                    ->widget(Redactor::class, !empty($config) ? $config : []);
            },
            self::FIELD_SMALL_REDACTOR => function ($attribute, $config, $containerConfig,
                                                    ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)->widget(Redactor::class,
                    ArrayHelper::merge(
                        [
                            'clientOptions' => [
                                'uploadImage' => false,
                                'uploadFile' => false,
                                'buttons' => ['html', 'bold', 'italic', 'deleted', 'link']
                            ]
                        ],
                        $config
                    )
                );
            },
            self::FIELD_CHECKBOX => function ($attribute, $config, $containerConfig,
                                              ActiveForm $form, ActiveRecord $model): ActiveField {
                return $form->field($model, $attribute, $containerConfig)
                    ->widget(CheckboxX::class, [
                        'pluginOptions' => [
                            'threeState' => false
                        ],
                        'autoLabel' => true
                    ])->label(false);
            }
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

    protected function printAttributeWidgets(ActiveForm $form, ActiveRecord $model)
    {
        $html = '';
        $tabs = [];
        $attributeWidgets = ArrayHelper::merge([
            'fields' => [],
            'options' => [
                'tabs' => false
            ]
        ], $this->getAttributeWidgets());
        if (!empty($attributeWidgets['fields'])) {
            foreach ($attributeWidgets['fields'] as $key => $value) {
                if ($attributeWidgets['options']['tabs'] === true && is_array($value)) {
                    foreach ($value as $key2 => $value2) {
                        if (empty($tabs[$key])) {
                            $tabs[$key] = $this->printWidget($key2, $value2, $form, $model);
                        } else {
                            $tabs[$key] .= $this->printWidget($key2, $value2, $form, $model);
                        }
                    }
                } else if ($attributeWidgets['options']['tabs'] === true) {
                    throw new InvalidConfigException('Массив fields у метода ' . __METHOD__ .
                        ' при включенной ' . 'табуляции должен массивом массивов полей'
                    );
                } else if ($attributeWidgets['options']['tabs'] !== true) {
                    $html .= $this->printWidget($key, $value, $form, $model);
                }
            }
        }
        if ($attributeWidgets['options']['tabs'] === true && !empty($tabs)) {
            $items = [];
            foreach ($tabs as $key => $item) {
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
        }

        return $html;
    }

    protected function printWidget($attribute, $config, ActiveForm $form, ActiveRecord $model)
    {
        $attributeWidgetsTypes = ArrayHelper::merge(
            $this->getDefaultAttributeWidgetsTypes(),
            $this->getAttributeWidgetsTypes()
        );
        if (is_array($config)) {
            $widget = ArrayHelper::remove($config, 0);
            $containerOptions = [];
            $labelOptions = [];
            $hintOptions = [];
            if (isset($config['containerOptions'])) {
                $containerOptions = ArrayHelper::remove($config, 'containerOptions');
            }
            if (isset($config['labelOptions'])) {
                $labelOptions = ArrayHelper::remove($config, 'labelOptions');
            }
            if (isset($config['hintOptions'])) {
                $hintOptions = ArrayHelper::remove($config, 'hintOptions');
            }
            $field = null;
            switch (!empty($attributeWidgetsTypes[$widget])) {
                case true:
                    /**
                     * @var ActiveField $field
                     * @var \Closure $callback
                     */
                    $callback = $attributeWidgetsTypes[$widget];
                    $field = $callback($attribute, $config, $containerOptions, $form, $model);
                    break;
                default:
                    $field = $form->field($model, $attribute)->widget($widget, $config);
                    break;
            }
            if(!empty($labelOptions)) {
                $field->label(
                    ArrayHelper::remove($labelOptions, 'content', false),
                    $labelOptions
                );
            }
            if(!empty($hintOptions)) {
                $field->hint(
                    ArrayHelper::remove($hintOptions, 'content', false),
                    $hintOptions
                );
            }
            return $field;
        } else if (is_bool($config) && $config === false) {
            return '';
        }
        return '';
    }

    public function getForm(ActiveForm $form, ActiveRecord $model): string
    {
        return $this->printAttributeWidgets($form, $model);
    }
}