<?php

namespace app\components\notification;

use app\models\notifications\Notification;
use app\models\Settings;
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
     * @param      $userId
     * @param null $taskId
     * @param null $message
     */
    public static function notifyUser($userId, $taskId = null, $message = null)
    {
        /** @var NotifyFactory[] $notifies */
        $notifies = [
            self::create(Notification::TYPE_INSIDE)
        ];
        $notifications = Users::findOne($userId)->getNotifications();

        foreach ($notifications as $key => $value) {
            switch ($key) {
                case Settings::USE_TELEGRAM:
                    $notifies[] = self::create(Notification::TYPE_TELEGRAM);
                    break;
                case Settings::USE_EMAIL:
                    $notifies = self::create(Notification::TYPE_MAIL);
                    break;
                default:
                    continue;
            }
        }

        foreach ($notifies as $notify) {
            $notify->setUserId($userId)
                ->setTaskId($taskId)
                ->setMessage($message);
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
    public function setTaskId(int $taskId)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * @param int $userId
     *
     * @return NotifyFactory
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return NotifyFactory
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }
}