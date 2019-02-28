<?php

namespace app\controllers;

use app\components\BaseController;
use app\components\notification\NotifyFactory;
use app\models\Files;
use app\models\Projects;
use app\models\Tasks;
use app\models\TasksSearch;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class TasksController extends BaseController
{
    public $defaultAction = 'my-tasks';

    /**
     * @param int $taskId
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTask(int $taskId)
    {
        $model = Tasks::find()
            ->select(['t.*', 'h.id AS history_id'])
            ->alias('t')
            ->withAllRelation()
            ->joinWith(['history h' => function (ActiveQuery $query) {
                return $query->orderBy(['h.id' => SORT_DESC]);
            }])
            ->where(['t.id' => $taskId])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException("Проект не найден");
        }

        return $this->render('parts/one-task', compact('model'));
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
            NotifyFactory::notifyUser($model->assigned_to, $model->id, 'Новая задача');
            \Yii::$app->session->setFlash('success', 'Задача успешно заведена');
            return $this->refresh();
        }

        return $this->render('form', compact('model', 'users', 'projects'));
    }

    /**
     * @param int $taskId
     *
     * @return string|\yii\web\Response
     */
    public function actionUpdate(int $taskId)
    {
        $model = Tasks::find()->where(['id' => $taskId])->one();

        $userModel = Users::find()
            ->withProfile()
            ->all();

        $users = ArrayHelper::map($userModel, 'id', 'profile.fullName');
        $projects = ArrayHelper::map(Projects::find()->all(), 'id', 'title');

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $model->files = UploadedFile::getInstances($model, 'files');
            if ($model->files) {
                $model->prepareFile();
            }
            NotifyFactory::notifyUser($model->assigned_to, $model->id, 'Задача обновлена');
            \Yii::$app->session->setFlash('success', 'Задача успешно обновлена');
            return $this->redirect(['tasks/task', 'taskId' => $model->project_id]);
        }

        return $this->render('form', compact('model', 'users', 'projects'));
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

    public function actionGantt()
    {
        return $this->render('gantt');
    }
}