<?php
/**
 * @var yii\web\View $this
 * @var \yii\db\ActiveRecord $model
 * @var \ofixone\content\models\FilterModel $filterModel
 *
 * @var \ofixone\content\Module $module
 */

use kartik\form\ActiveForm;

$module = Yii::$app->controller->module;
$this->title = \ofixone\content\helpers\StringHelper::mb_ucfirst(
    'Добавить ' . mb_strtolower($module->names[
        $module::NAME_ONE
    ])
);
$this->params['breadcrumbs'] = [
    [
        'label' => \ofixone\content\helpers\StringHelper::mb_ucfirst(
            $module->names[$module::NAME_COUPLE]
        ),
        'url' => ['list']
    ], [
        'label' => $this->title
    ]
];
?>
<?php $form = ActiveForm::begin() ?>
    <?= $filterModel->getForm($form, $model); ?>
    <div class="box box-default">
        <div class="box-body">
            <?= \yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            <?= \yii\helpers\Html::submitButton('Сохранить и вернуться',
                [
                    'class' => 'btn btn-success',
                    'name' => 'save-back'
                ])
            ?>
        </div>
    </div>
<?php ActiveForm::end() ?>