<?php

namespace ofixone\content\controllers;

use kartik\grid\EditableColumnAction;
use ofixone\admin\widgets\alert\Widget;
use ofixone\filekit\CropAction;
use ofixone\filekit\UploadAction;
use trntv\filekit\actions\DeleteAction;
use yii\web\Controller;
use ofixone\content\models\AdminModel;
use ofixone\content\Module;
use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ModuleController
 * @property Module $module
 * @package ofixone\content\controllers
 */
class ModuleController extends Controller
{
    public function beforeAction($action)
    {
        if (in_array($action->id, ['list'])) {
            Url::remember();
        }
        if($this->module->type !== Module::TYPE_MULTIPLE && in_array($action->id, ['create', 'list', 'delete'])) {
            throw new BadRequestHttpException();
        }
        return true;
    }

    public function actions()
    {
        return [
            'upload' => [
                'class' => UploadAction::class,
                'deleteRoute' => 'upload-delete',
            ],
            'upload-delete' => [
                'class' => DeleteAction::class,
            ],
            'crop' => [
                'class' => CropAction::class
            ],
            'editable' => [
                'class' => EditableColumnAction::class,
                'modelClass' => $this->module->model
            ]
        ];
    }


    public function actionList()
    {
        /**
         * @var ActiveRecord $modelClass
         * @var ActiveRecord $model
         * @var AdminModel $adminModel
         */
        $modelClass = $this->module->model;
        $model = new $modelClass;
        $adminModel = new $this->module->adminModel;
        $adminModel->load(Yii::$app->request->get());
        $dataProvider = $adminModel->getDataProvider();
        return $this->render('list', [
            'model' => $model,
            'adminModel' => $adminModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id = 1)
    {
        if((
            $this->module->disableUpdate ? call_user_func($this->module->disableUpdate, $this->module) : false
        ) === true) {
            throw new ForbiddenHttpException('Обновление для ' .
                $this->module->names[Module::NAME_ONE] .
                ' запрещено');
        }
        /**
         * @var $model ActiveRecord
         * @var $class ActiveRecord
         */
        $class = $this->module->model;
        $model = $class::find()->where(['id' => $id])->limit(1)->one();
        if (empty($model)) throw new NotFoundHttpException();
        $adminModel = new $this->module->adminModel([
            'model' => $model
        ]);
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('alert', [
                    'type' => Widget::TYPE_SUCCESS,
                    'heading' => 'Сохранено!',
                    'text' => 'Запись успешно обновлена',
                    'position' => 'top-right'
                ]);
                if ($this->module->type === Module::TYPE_MULTIPLE && Yii::$app->request->post('save-back') !== null) {
                    return $this->redirect(Url::previous());
                }
            } else {
                Yii::$app->session->setFlash('alert', [
                    'js' => true,
                    'icon' => 'success',
                    'heading' => 'Ошибка!',
                    'text' => 'При сохранении данных произошла ошибка: '
                        . implode(",", $model->getFirstErrors()),
                    'position' => 'top-right'
                ]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'adminModel' => $adminModel
        ]);
    }

    public function actionCreate()
    {
        /**
         * @var $model ActiveRecord
         */
        if((
            $this->module->disableCreate ? call_user_func($this->module->disableCreate, $this->module) : false
        ) === true) {
            throw new ForbiddenHttpException('Создание для ' .
                $this->module->names[Module::NAME_ONE] .
            ' запрещено');
        }
        $model = new $this->module->model;
        $adminModel = new $this->module->adminModel([
            'model' => $model
        ]);
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('alert', [
                    'type' => Widget::TYPE_SUCCESS,
                    'heading' => 'Сохранено!',
                    'text' => 'Новая запись успешно добавлена',
                    'position' => 'top-right'
                ]);
                if (Yii::$app->request->post('save-back') !== null) {
                    return $this->redirect(Url::previous());
                } else {
                    return $this->redirect(['update', 'id' => $model->getPrimaryKey()]);
                }
            } else {
                Yii::$app->session->setFlash('alert', [
                    'js' => true,
                    'icon' => 'error',
                    'heading' => 'Ошибка!',
                    'text' => 'При сохранении данных произошла ошибка: '
                        . implode(",", $model->getFirstErrors()),
                    'position' => 'top-right'
                ]);
            }
        }
        $model->loadDefaultValues();
        return $this->render('create', [
            'model' => $model,
            'adminModel' => $adminModel
        ]);
    }

    public function actionDelete($id)
    {
        /**
         * @var $model ActiveRecord
         * @var $class ActiveRecord
         */
        if((
            $this->module->disableDelete ? call_user_func($this->module->disableDelete, $this->module) : false
        ) === true) {
            throw new ForbiddenHttpException('Удаление для ' .
                $this->module->names[Module::NAME_ONE] .
            ' запрещено');
        }
        $class = $this->module->model;
        $model = $class::find()->where(['id' => $id])->limit(1)->one();
        if (empty($model)) {
            throw new NotFoundHttpException();
        } else {
            $model->delete();
            Yii::$app->session->setFlash('alert', [
                'js' => true,
                'icon' => 'success',
                'heading' => 'Удалено!',
                'text' => 'Запись успешно удалена',
                'position' => 'top-right'
            ]);
        }
        return $this->redirect(Url::previous());
    }
}