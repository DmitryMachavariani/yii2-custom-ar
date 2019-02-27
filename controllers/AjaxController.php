<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Files;
use app\models\History;
use app\models\notifications\Notification;
use app\models\Tasks;
use app\models\Trackers;
use app\models\Users;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * @property array $post
 */
class AjaxController extends BaseController
{
    private $post;
    /**
     * @param $action
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!\Yii::$app->request->isAjax)
        {
            return false;
        }

        $this->post = \Yii::$app->request->post();
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionNotifyRead()
    {
        $count = Notification::setNotifyRead();
        return ["msg" => "Прочитано {$count} уведомлений", "type" => "success"];
    }

    public function actionStatus ()
    {
        $taskId = $this->post['taskId'] ?? '';
        $status = $this->post['statuses'] ?? '';
        $statuses = Tasks::STATUSES;

        if (!empty($taskId) && $status !== '')
        {
            $task = Tasks::find()->where(['id' => $taskId])->one();

            if (!$task)
            {
                return false;
            }

            $task->setStatus($status);
            $statusName = Tasks::getStatus($status);
            History::create(History::TYPE_CHANGE_STATUS, History::MODEL_TASKS, $task->id);

            return ["msg" => "Статус изменен на {$statusName}", "type" => "success"];
        }

        return $this->renderAjax('statuses', compact('statuses', 'taskId'));
    }

    public function actionAssigned ()
    {
        $taskId = $this->post['taskId'] ?? '';
        $userId = $this->post['userId'] ?? '';
        $userModel = Users::find()
            ->withProfile()
            ->all();

        $users = ArrayHelper::map($userModel, 'id', 'profile.fullName');

        if (!empty($taskId) && $userId !== '')
        {
            $task = Tasks::find()->where(['id' => $taskId])->one();

            if (!$task)
            {
                return false;
            }

            $task->setAssigned($userId);
            $userName = Users::find()->where(['id' => $userId])->one();
            History::create(History::TYPE_CHANGE_ASSIGN_TO, History::MODEL_TASKS, $task->id);


            return ["msg" => "Исполнитель изменен на {$userName->username}", "type" => "success"];
        }

        return $this->renderAjax('assigment', compact('users', 'taskId'));
    }

    public function actionTrack ()
    {
        $taskId = $this->post['taskId'] ?? '';
        $action = $this->post['action'] ?? '';
        $time = $this->post['time'] ?? '';
        $comment = $this->post['comment'] ?? '';

        $actions = Trackers::TYPES;

        if (is_numeric($action)) {
            $task = Tasks::find()->where(['id' => $taskId])->one();

            if (!$task) {
                return false;
            }

            $model = new Trackers();
            $model->task_id = $taskId;
            $model->action = $action;
            $model->time = $time;
            $model->comment = $comment;

            if ($model->save()) {
                return ["msg" => "Успех", "type" => "success"];
            } else {
                return ["msg" => "Возникла ошибка. Проверьте правильность заполнения данных", "type" => "fail"];
            }
        }

        return $this->renderAjax('track', compact('taskId', 'actions'));
    }

    /**
     * @param $file
     *
     * @return array
     * @throws \Throwable
     */
    public function actionRemoveFile($file)
    {
        try {
            $fileModel = Files::findOne($file);
            if (!$file) {
                throw new NotFoundHttpException('Файл не найден');
            }
            if (!$fileModel->delete()) {
                throw new \LogicException('Невозможно удалить файл');
            }
            return [
                'status' => 1,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 0,
                'message' => $e->getMessage()
            ];
        }
    }
}