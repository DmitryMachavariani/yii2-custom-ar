<?php

namespace app\components\notification;

use app\models\notifications\Notification;

abstract class NotifyFactory implements \yii\queue\JobInterface
{
    /**
     * @var integer
     */
    protected $taskId;

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
}