<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\notifications\Notification;
use app\models\Tasks;
use app\models\Users;
use yii\helpers\ArrayHelper;
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
}