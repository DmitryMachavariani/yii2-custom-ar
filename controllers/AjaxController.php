<?php

namespace app\controllers;

use app\components\BaseController;
use app\components\notification\NotifyFactory;
use app\models\Files;
use app\models\GanttForm;
use app\models\notifications\Notification;
use app\models\TaskComment;
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
                return ["msg" => "Возникла ошибка. Проверьте правильность заполнения данных", "type" => "danger"];
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

    /**
     * @return array
     * @throws \Exception
     */
    public function actionTasks()
    {
        if (isset($_GET['test'])) {
            return $this->testTasks();
        }
        $searchModel = new GanttForm();
        $params = \Yii::$app->request->queryParams;
        $searchModel->searchTasks($params, \Yii::$app->request->get('page', 1));
        list($data, $links) = $searchModel->formatTasks();
        $pagination = $searchModel->paginationData;

        return compact('data', 'links', 'pagination');
    }

    /**
     * @return array
     */
    protected function testTasks()
    {
        return GanttForm::getTestTask();
    }

    public function actionTracks()
    {
        $taskId = $this->post['taskId'] ?? '';

        $trackers = Trackers::find()
            ->alias('t')
            ->withUser()
            ->where(['t.task_id' => $taskId])
            ->all();

        return $this->renderAjax('tracks', compact('trackers'));
    }

    /**
     * @param int $trackId
     *
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemoveTrack(int $trackId)
    {
        $track = Trackers::findOne($trackId);

        if ($track)
        {
            $track->delete();
            return ['type' => 'success', 'msg' => 'Успешно удалено'];
        }

        return ['type' => 'danger', 'msg' => 'Возникли ошибки.'];
    }

    public function actionAddComment($commentId = null)
    {
        $model = ($commentId ? TaskComment::findOne($commentId) : new TaskComment());
        $model->load(\Yii::$app->request->post());
        if (!$model->validate()) {
            return [
                'status' => 0,
                'message' => implode(PHP_EOL, $model->getErrors())
            ];
        }
        $model->save();
        NotifyFactory::notifyUser(
            $model->task->getUsersToNotify(),
            $model->task->id,
            [
                'view' => 'new-comment',
                'comment' => $model->text
            ]
        );

        return ['status' => 1];
    }
}