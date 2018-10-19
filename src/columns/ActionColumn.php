<?php

namespace ofixone\content\columns;

use microinginer\dropDownActionColumn\DropDownActionColumn;

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
                'data-method' => 'post'
            ],
        ],
    ];
}