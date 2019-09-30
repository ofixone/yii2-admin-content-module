<?php

namespace ofixone\content\columns;

use microinginer\dropDownActionColumn\DropDownActionColumn;
use Yii;
use yii\grid\Column;
use yii\helpers\Html;

class ActionColumn extends DropDownActionColumn
{
    public $items = [
        [
            'label' => 'Изменить',
            'url' => ['update'],
        ],
        [
            'label' => 'Удалить',
            'url' => ['delete'],
            'linkOptions' => [
                'data-method' => 'post',
                'data-confirm' => 'Вы действительно хотите удалить эту запись?',
            ],
        ],
    ];
    public $activeButtons = [];
    public $defaultItems = false;

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $result = '';

        if ($this->defaultItems) {
            $this->items = [
                [
                    'label' => Yii::t('yii', 'Update'),
                    'url' => ['update'],
                    'visible' => true,
                ],
                [
                    'label' => Yii::t('yii', 'View'),
                    'url' => ['view'],
                    'visible' => true,
                ],
                [
                    'label' => Yii::t('yii', 'Delete'),
                    'url' => ['delete'],
                    'linkOptions' => [
                        'data-method' => 'post'
                    ],
                    'visible' => true,
                ],
            ];
        }

        foreach ($this->items as $key => $value) {
            if (isset($value['visible']) && false === $value['visible']) {
                unset($this->items[$key]);
            }
        }

        $firstKey = current(array_keys($this->items));
        $mainBtn = $this->items[$firstKey];

        $result .= Html::a($mainBtn['label'], array_merge($mainBtn['url'], [$model->primaryKey()[0] => $model->getPrimaryKey()]), array_merge(
                ['class' => 'btn btn-default btn-sm'],
                isset($mainBtn['linkOptions']) ? $mainBtn['linkOptions'] : [])
        );

        $unavailableItems = 0;
        if (count($this->items) != 1) {
            $result .= Html::button(
                Html::tag('span', '', ['class' => 'caret']) .
                Html::tag('span', 'Toggle Dropdown', ['class' => 'sr-only']), [
                    'class' => 'btn btn-default btn-sm dropdown-toggle',
                    'data-toggle' => 'dropdown',
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'false',
                ]
            );

            $items = '';
            $firstElement = true;

            foreach ($this->items as $itemIndex => $item) {

                if(
                    isset($this->activeButtons[$itemIndex]) && call_user_func(
                        $this->activeButtons[$itemIndex], $model, $key, $index
                    ) === false
                ) {
                    $unavailableItems++;
                }

                if (isset($this->visibleButtons[$itemIndex])) {
                    $isVisible = $this->visibleButtons[$itemIndex] instanceof \Closure
                        ? call_user_func($this->visibleButtons[$itemIndex], $model, $key, $index)
                        : $this->visibleButtons[$itemIndex];
                } else {
                    $isVisible = true;
                }
                if ($isVisible) {

                    if ($firstElement) {
                        $firstElement = false;
                        continue;
                    }

                    $items .= Html::tag(
                        'li',
                        Html::a(
                            $item['label'],
                            array_merge($item['url'], [$model->primaryKey()[0] => $model->getPrimaryKey()]),
                            (isset($item['linkOptions']) ? $item['linkOptions'] : ['class' => 'dropdown-item'])
                        ),
                        (isset($item['options']) ? $item['options'] : []));
                }
            }
            $result .= Html::tag('ul', $items, ['class' => 'dropdown-menu dropdown-menu-right']);
        }

        return count($this->items) !== $unavailableItems ?
            Html::tag('div', $result, ['class' => 'btn-group pull-right', 'style' => 'display: flex']) : '';
    }
}