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
    'Обновить ' . mb_strtolower($module->names[$module::NAME_ONE])
);
$this->params['breadcrumbs'] = !empty($module->names[$module::NAME_MULTIPLE]) ? [
    [
        'label' => \ofixone\content\helpers\StringHelper::mb_ucfirst(
            $module->names[$module::NAME_MULTIPLE]
        ),
        'url' => ['list']
    ], [
        'label' => $this->title
    ]
] : [
    [
        'label' => $this->title
    ]
];
?>
<?php $form = ActiveForm::begin() ?>
    <div class="box box-default">
        <div class="box-body">
            <?= $filterModel->getForm($form, $model); ?>
        </div>
    </div>
    <div class="box box-default">
        <div class="box-body">
            <?= \yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            <?= \yii\helpers\Html::submitButton('Сохранить и вернуться',
                [
                    'class' => 'btn btn-success',
                    'name' => 'save-back'
                ])
            ?>
            <?php if ($module->disableDelete === false): ?>
                <?= \yii\helpers\Html::a(
                    'Удалить',
                    ['delete', 'id' => $model->getPrimaryKey()],
                    [
                        'class' => 'btn btn-danger',
                        'data-confirm' => 'Вы действительно хотите удалить эту запись?',
                        'data-method' => 'post'
                    ]
                ) ?>
            <?php endif; ?>
        </div>
    </div>
<?php ActiveForm::end() ?>