<?php

namespace ofixone\content\controllers;

use yii\web\Controller;

class ModuleController extends Controller
{
    public function actionList()
    {
        return $this->render('list');
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionDelete()
    {

    }
}