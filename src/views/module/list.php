<?php
/**
 * @var \yii\web\View $this
 * @var \yii\db\ActiveRecord $model
 * @var \ofixone\content\models\FilterModel $filterModel
 * @var \yii\data\ActiveDataProvider $dataProvider
 *
 * @var \ofixone\content\Module $module
 */
$module = Yii::$app->controller->module;
$this->title = \ofixone\content\helpers\StringHelper::mb_ucfirst($module->names[
    $module::NAME_MULTIPLE
]);
$this->params['breadcrumbs'] = [
    [
        'label' => $this->title,
        'url' => ['list']
    ],
];
?>
<div class="box box-default">
    <div class="box-body">
        <?= \kartik\grid\GridView::widget([
            'tableOptions' => [
                'class' => 'table table-bordered',
                'style' => 'margin-bottom: 0px'
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'resizableColumns' => true,
            'hover' => true,
            'responsive' => false,
            'persistResize' => true,
            'pager' => ['options' => ['class' => 'pagination no-margin']],
            'columns' => $filterModel->getGridColumns(),
            'toolbar' => [
                '{toggleData}'
            ],
            'panel' => [
                'heading' => false,
                'type' => 'default',
                'before' => $module->disableCreate == false ? \yii\helpers\Html::a(
                    'Добавить ' . mb_strtolower($module->names[
                        $module::NAME_ONE
                    ]),
                    ['create'],
                    ['class' => 'btn btn-success']
                ) : '',
                'after' => !empty(Yii::$app->request->get()) ?
                    \yii\helpers\Html::a(
                        'Очистить фильтры',
                        ['list'],
                        ['class' => 'btn btn-info']
                    )
                    : false
            ],
        ]) ?>
    </div>
</div>