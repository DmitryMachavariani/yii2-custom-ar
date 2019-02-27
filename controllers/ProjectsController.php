<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Projects;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

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

        return $this->render('form', compact('model'));
    }

    /**
     * @param int $projectId
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $projectId)
    {
        $model = Projects::find()->where(['id' => $projectId])->one();

        if (!$model) {
            throw new NotFoundHttpException("Проект не найден");
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->session->setFlash('success', 'Проект успешно обновлен');
            return $this->redirect(['projects/index']);
        }

        return $this->render('form', compact('model'));
    }
}