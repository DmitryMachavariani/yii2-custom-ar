<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Projects;
use yii\data\ActiveDataProvider;

class ProjectsController extends BaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'sort' => false,
            'query' => Projects::find()
                ->alias('p')
                ->addSelect('p.*')
                ->percentOfCompleteTasks()
                ->withTasks()
                ->groupBy('p.id')
        ]);

        return $this->render('index', compact('dataProvider'));
    }

    public function actionCreate()
    {
        $model = new Projects();

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->session->setFlash('success', 'Проект успешно создан');
            return $this->redirect(['projects/index']);
        }

        return $this->render('create', compact('model'));
    }
}