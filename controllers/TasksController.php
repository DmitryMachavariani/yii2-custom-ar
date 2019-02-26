<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Projects;
use app\models\Tasks;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class TasksController extends BaseController
{
    public $defaultAction = 'my-tasks';

    public function actionTask(int $taskId)
    {
        $dataProvider = new ActiveDataProvider([
            'sort' => false,
            'query' => Tasks::find()
                ->alias('t')
                ->withAllRelation()
                ->where(['t.id' => $taskId])
                ->orderBy(['h.id' => SORT_DESC])
        ]);

        return $this->render('task', compact('dataProvider'));
    }

    public function actionMyTasks()
    {
        $projectId = null;

        $dataProvider = new ActiveDataProvider([
            'sort' => false,
            'query' => Tasks::find()
                ->withAllRelation()
                ->my()
                ->orderBy(['h.id' => SORT_DESC])
        ]);

        return $this->render('tasks', compact('dataProvider', 'projectId'));
    }

    public function actionTasks(int $projectId = null)
    {
        $dataProvider = new ActiveDataProvider([
            'sort' => false,
            'query' => Tasks::find()->byProject($projectId)
        ]);

        return $this->render('tasks', compact('dataProvider', 'projectId'));
    }

    public function actionCreate(int $projectId = null)
    {
        $model = new Tasks();

        $userModel = Users::find()
            ->withProfile()
            ->all();

        $users = ArrayHelper::map($userModel, 'id', 'profile.fullName');
        $projects = ArrayHelper::map(Projects::find()->all(), 'id', 'title');

        if (!is_null($projectId))
        {
            $model->project_id = $projectId;
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->session->setFlash('success', 'Задача успешно заведена');
            return $this->refresh();
        }

        return $this->render('create', compact('model', 'users', 'projects'));
    }
}