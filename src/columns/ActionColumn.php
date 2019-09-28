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
                'data-method' => 'post',
                'data-confirm' => 'Вы действительно хотите удалить эту запись?',
            ],
        ],
    ];
    public $activeButtons = [];

    public function init()
    {
        parent::init();
        foreach($this->items as $key => $item) {
            if(isset($this->activeButtons[$key]) && $this->activeButtons[$key] === false) {
                unset($this->items[$key]);
            }
        }
        if(empty($this->items)) {
            $this->visible = false;
        }
    }
}