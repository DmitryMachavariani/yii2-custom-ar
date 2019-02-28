<?php

namespace app\components\notification;

use app\models\notifications\Notification;
use app\models\Settings;
use app\models\Tasks;
use app\models\Users;

abstract class NotifyFactory implements \yii\queue\JobInterface
{
    /**
     * @var integer
     */
    protected $taskId;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var Tasks
     */
    protected $task;

    /**
     * @param       $userId
     * @param null  $taskId
     * @param array $params
     * @param null  $message
     */
    public static function notifyUser($userId, $taskId = null, $params = [], $message = null)
    {
        /** @var NotifyFactory[] $notifies */
        $notifies = [
            self::create(Notification::TYPE_INSIDE)
        ];
        $notifications = Users::findOne($userId)->getNotifications();
        $task = $taskId ? Tasks::findOne($taskId) : null;

        foreach ($notifications as $key => $value) {
            switch ($key) {
                case Settings::USE_TELEGRAM:
                    $notifies[] = self::create(Notification::TYPE_TELEGRAM);
                    break;
                case Settings::USE_EMAIL:
                    $notifies[] = self::create(Notification::TYPE_MAIL);
                    break;
                default:
                    continue;
            }
        }

        foreach ($notifies as $notify) {
            $notify->setUserId($userId)
                ->setTaskId($taskId)
                ->setMessage($message)
                ->setParams($params)
                ->setTask($task);
            \Yii::$app->queue->push($notify);
        }
    }

    /**
     * @param string $type
     *
     * @return NotifyFactory
     */
    public static function create(string $type)
    {
        switch ($type) {
            case Notification::TYPE_INSIDE:
                return new NotificationInside();
                break;
            case Notification::TYPE_TELEGRAM:
                return new NotificationTelegram();
                break;
            case Notification::TYPE_MAIL:
                return new NotificationEmail();
                break;
        }
    }

    public function execute($queue)
    {
        return $this->send();
    }

    abstract function send();

    /**
     * @param int $taskId
     *
     * @return NotifyFactory
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * @param int $userId
     *
     * @return NotifyFactory
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return NotifyFactory
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return NotifyFactory
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }
}