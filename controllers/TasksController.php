<?php

namespace app\controllers;

use app\components\BaseController;
use app\components\notification\NotifyFactory;
use app\models\Files;
use app\models\Projects;
use app\models\TaskComment;
use app\models\Tasks;
use app\models\TasksSearch;
use app\models\Users;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
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
        $myTasks = true;

        $searchModel = new TasksSearch();
        $dataProvider = $searchModel->searchGrid(\Yii::$app->request->queryParams, null, true);
        $statuses = Tasks::STATUSES;

        return $this->render('tasks', compact('dataProvider', 'searchModel', 'statuses', 'projectId', 'myTasks'));
    }

    public function actionTasks(int $projectId = null)
    {
        $myTasks = false;
        $searchModel = new TasksSearch();
        $dataProvider = $searchModel->searchGrid(\Yii::$app->request->queryParams, $projectId, false);
        $statuses = Tasks::STATUSES;

        return $this->render('tasks', compact('dataProvider', 'searchModel', 'statuses', 'projectId', 'myTasks'));
    }

    /**
     * @param int|null $projectId
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate(int $projectId = null)
    {
        $model = new Tasks();
        $commentModel = new TaskComment();

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
            NotifyFactory::notifyUser($model->getUsersToNotify(), $model->id, [
                'view' => 'new_task'
            ]);
            \Yii::$app->session->setFlash('success', 'Задача успешно заведена');
            return $this->redirect(['tasks/task', 'taskId' => $model->id]);
        }

        return $this->render('form', compact('model', 'users', 'projects', 'commentModel'));
    }

    /**
     * @param int $taskId
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $taskId)
    {
        $model = Tasks::find()->where(['id' => $taskId])->one();
        $commentModel = new TaskComment();

        if (!$model) {
            throw new NotFoundHttpException("Задача не найдена");
        }

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
            NotifyFactory::notifyUser($model->getUsersToNotify(), $model->id, [
                'view' => 'edit_task'
            ]);
            \Yii::$app->session->setFlash('success', 'Задача успешно обновлена');
            return $this->redirect(['tasks/task', 'taskId' => $model->id]);
        }

        return $this->render('form', compact('model', 'users', 'projects', 'commentModel'));
    }

    /**
     * @param $file_id
     *
     * @return bool
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDownload($file_id)
    {
        $file = Files::findOne($file_id);
        if (!$file) {
            throw new NotFoundHttpException('Файл не найден');
        }
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        if (!($result = $file->getFile())) {
            throw new HttpException(400, 'Проблема с файлом');
        }

        return $result;
    }

    /**
     * @param $file_id
     *
     * @return bool|Response
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionViewFile($file_id)
    {
        $file = Files::findOne($file_id);
        if (!$file) {
            throw new NotFoundHttpException('Файл не найден');
        }
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        if (!($result = $file->getFile(false))) {
            throw new HttpException(400, 'Проблема с файлом');
        }

        return $result;
    }

    public function actionSearch()
    {
        $projectId = null;
        $dataProvider = TasksSearch::search(\Yii::$app->request->get('q', null));
        $searchModel = new TasksSearch();
        $statuses = Tasks::STATUSES;
        $myTasks = false;

        return $this->render('tasks', compact('dataProvider', 'projectId', 'searchModel', 'statuses', 'myTasks'));
    }

    public function actionGantt()
    {
        return $this->render('gantt');
    }
}