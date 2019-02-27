<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Files;
use app\models\Projects;
use app\models\Tasks;
use app\models\TasksSearch;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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

    /**
     * @param int|null $projectId
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate(int $projectId = null)
    {
        $model = new Tasks();

        $userModel = Users::find()
            ->withProfile()
            ->all();

        $users = ArrayHelper::map($userModel, 'id', 'profile.fullName');
        $projects = ArrayHelper::map(Projects::find()->all(), 'id', 'title');

        if (!is_null($projectId)) {
            $model->project_id = $projectId;
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model->files = UploadedFile::getInstances($model, 'files');
            if ($model->files) {
                $model->prepareFile();
            }
            \Yii::$app->session->setFlash('success', 'Задача успешно заведена');
            return $this->refresh();
        }

        return $this->render('create', compact('model', 'users', 'projects'));
    }

    /**
     * @param $file_id
     *
     * @return \yii\console\Response|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDownload($file_id)
    {
        $file = Files::findOne($file_id);
        if (!$file) {
            throw new NotFoundHttpException('Файл не найден');
        }
        $mimeType = $file->getMimeType();
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', "{$mimeType}; charset=utf-8");

        return \Yii::$app->response->sendFile($file->getFullPath());
    }

    public function actionSearch()
    {
        $projectId = null;
        $dataProvider = TasksSearch::search(\Yii::$app->request->get('q', null));

        return $this->render('tasks', compact('dataProvider', 'projectId'));
    }
}