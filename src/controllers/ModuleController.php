<?php

namespace ofixone\content\controllers;

use yii\web\Controller;
use ofixone\content\models\FilterModel;
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

    public function actionList()
    {
        /**
         * @var ActiveRecord $modelClass
         * @var ActiveRecord $model
         * @var FilterModel $filterModel
         */
        $modelClass = $this->module->model;
        $model = new $modelClass;
        $filterModel = new $this->module->filterModel;
        $filterModel->load(Yii::$app->request->get());
        $dataProvider = $filterModel->getDataProvider();
        return $this->render('list', [
            'model' => $model,
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id = 1)
    {
        /**
         * @var $model ActiveRecord
         * @var $class ActiveRecord
         */
        $class = $this->module->model;
        $model = $class::find()->where(['id' => $id])->limit(1)->one();
        if (empty($model)) throw new NotFoundHttpException();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('alert', [
                    'js' => true,
                    'icon' => 'success',
                    'heading' => 'Сохранено!',
                    'text' => 'Новая запись успешно сохранена',
                    'position' => 'top-right'
                ]);
                if ($this->module->type === Module::TYPE_MULTIPLE && Yii::$app->request->post('save-back') !== null) {
                    return $this->redirect(Url::previous());
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
        return $this->render('update', [
            'model' => $model,
            'filterModel' => new $this->module->filterModel
        ]);
    }

    public function actionCreate()
    {
        /**
         * @var $model ActiveRecord
         */
        if($this->module->disableCreate == true) {
            throw new ForbiddenHttpException('Создание для ' .
                $this->module->names[Module::NAME_ONE] .
            ' запрещено');
        }
        $model = new $this->module->model;
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('alert', [
                    'icon' => 'success',
                    'heading' => 'Сохранено!',
                    'text' => 'Новая запись успешно добавлена',
                    'position' => 'top-right'
                ]);
                if (Yii::$app->request->post('save-back') !== null) {
                    return $this->redirect(Url::previous());
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
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
        return $this->render('create', [
            'model' => $model,
            'filterModel' => new $this->module->filterModel
        ]);
    }

    public function actionDelete($id)
    {
        /**
         * @var $model ActiveRecord
         * @var $class ActiveRecord
         */
        if($this->module->disableDelete == true) {
            throw new ForbiddenHttpException('Удаление для ' .
                $this->module->names[Module::NAME_ONE] .
            ' запрещено');
        }
        $class = $this->module->model;
        $model = $class::find()->where(['id' => $id])->limit(1)->one();
        if (empty($model)) {
            throw new NotFoundHttpException();
        } else {
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