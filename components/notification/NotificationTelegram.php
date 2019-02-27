<?php

namespace app\components\notification;

use app\models\Users;
use yii\base\Exception;

/**
 * Class NotificationTelegram.
 *
 * @package app\components\notification
 */
class NotificationTelegram extends NotifyFactory
{
    public function send()
    {
        if (empty($this->userId) || !($user = Users::findOne($this->userId)) || !$user->hasTelegram()) {
            return false;
        }

        \Yii::$app->bot->sendCustomChat($user->getTelegramId(), $this->message);
    }
}
