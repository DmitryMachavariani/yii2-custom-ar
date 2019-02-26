<?php

namespace app\components\notification;

use app\models\notifications\Notification;

abstract class NotifyFactory
{
    /**
     * @var integer
     */
    protected $taskId;

    public static function create(string $type)
    {
        switch ($type) {
            case Notification::TYPE_INSIDE:
                return new NotificationInside();
                break;
        }
    }

    abstract function send();

    /**
     * @param int $taskId
     */
    public function setTaskId(int $taskId): void
    {
        $this->taskId = $taskId;
    }
}