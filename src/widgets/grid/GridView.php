<?php
/**
 * Created by PhpStorm.
 * User: Vladimir Galtsev <ofixmail@yandex.ru>
 * Date: 28/09/2019
 * Time: 18:34
 */

namespace ofixone\content\widgets\grid;


use ofixone\content\models\AdminModel;
use yii\base\Widget;

class GridView extends Widget
{
    /**
     * @var AdminModel $adminModel
     */
    public $adminModel;
    public $createButton = [];
    public $options = [];
    public $heading = 'Данные';

    public function init()
    {
        parent::init();
        if(empty($this->createButton) && $this->createButton !== false) {
            $this->createButton = [
                'label' => 'Добавить',
                'url' => ['create'],
                'class' => ['btn', 'btn-success']
            ];
        }
    }

    public function run()
    {
        return $this->render('widget', [
            'widget' => $this
        ]);
    }
}