<?php
/**
 * Created by PhpStorm.
 * User: Vladimir Galtsev <ofixmail@yandex.ru>
 * Date: 28/09/2019
 * Time: 18:35
 */
/**
 * @var \yii\web\View $this
 * @var \ofixone\content\widgets\grid\GridView $widget
 */
$get = Yii::$app->request->get();
if(!empty($get[$widget->adminModel->formName()])) {
    unset($get[$widget->adminModel->formName()]);
}
$pjaxId = $widget->adminModel->formName() . '-pjax';
$exportDataProvider = clone $widget->adminModel->getDataProvider();
$exportDataProvider->pagination = false;
$export = \kartik\export\ExportMenu::widget([
    'pjaxContainerId' => $pjaxId,
    'dataProvider' => $exportDataProvider,
    'showConfirmAlert' => false,
    'clearBuffers' => true,
    'target' => \kartik\export\ExportMenu::TARGET_BLANK,
    'filename' => $widget->adminModel->getGridExportFilename() . ' (' .
        Yii::$app->formatter->asDatetime(time(),'php:d-m-Y_H-i-s')
    . ')',
    'exportConfig' => [
        \kartik\export\ExportMenu::FORMAT_HTML => false,
        \kartik\export\ExportMenu::FORMAT_TEXT => false,
        \kartik\export\ExportMenu::FORMAT_CSV => false,
        \kartik\export\ExportMenu::FORMAT_EXCEL => false,
    ],
    'columns' => $widget->adminModel->getGridExportColumns()
])
?>
<?php \yii\widgets\Pjax::begin([
    'id' => $pjaxId
]) ?>
<?= \kartik\grid\GridView::widget(\yii\helpers\ArrayHelper::merge([
    'tableOptions' => [
        'class' => 'table',
        'style' => 'margin-bottom: 0px'
    ],
    'dataProvider' => $widget->adminModel->getDataProvider(),
    'filterModel' => $widget->adminModel,
    'resizableColumns' => true,
    'hover' => true,
    'responsive' => false,
    'responsiveWrap' => false,
    'persistResize' => true,
    'striped' => false,
    'pager' => ['options' => ['class' => 'pagination no-margin']],
    'columns' => $widget->adminModel->getGridColumns(),
    'toolbar' => [
        [
            'content' => $export
        ],
        '{toggleData}'
    ],
    'panel' => [
        'heading' => $widget->heading,
        'before' => !empty($widget->createButton) ? \app\helpers\Html::a(
            \yii\helpers\ArrayHelper::remove($widget->createButton, 'label'),
            \yii\helpers\ArrayHelper::remove($widget->createButton, 'url'),
            $widget->createButton
        ) : null,
        'after' => !empty(Yii::$app->request->get($widget->adminModel->formName())) ?
            \yii\helpers\Html::a(
                'Очистить фильтры',
                \yii\helpers\ArrayHelper::merge([''], $get),
                ['class' => 'btn btn-info']
            )
            : false
    ],
], $widget->options)) ?>
<?php \yii\widgets\Pjax::end() ?>
