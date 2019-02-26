<?php
namespace app\components\notification;

use app\models\notifications\Notification;

class NotificationInside extends NotifyFactory
{
    /**
     * @var integer
     */
    protected $userId;

    public function send()
    {
        $model = new Notification();
        $model->type = Notification::TYPE_INSIDE;
        $model->status = Notification::STATUS_IN_PROGRESS;
        $model->user_id = $this->userId;
        $model->task_id = $this->taskId;
        $model->description = 'На вас заведена задача';

        $model->save();
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}