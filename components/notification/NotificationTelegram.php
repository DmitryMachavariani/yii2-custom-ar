<?php

namespace app\components\notification;

use app\models\Users;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

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
        $this->params = ArrayHelper::merge($this->params, ['user' => $user, 'task' => $this->task]);
        \Yii::$app->bot->sendCustomChat($user->getTelegramId(), $this->params);
    }
}
