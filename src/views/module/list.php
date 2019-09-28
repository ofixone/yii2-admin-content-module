<?php
/**
 * @var \yii\web\View $this
 * @var \yii\db\ActiveRecord $model
 * @var \ofixone\content\models\AdminModel $adminModel
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
        <?= $adminModel->getGrid() ?>
    </div>
</div>