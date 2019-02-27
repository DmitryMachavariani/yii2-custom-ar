<?php

namespace app\components\notification;

use app\models\Users;
use yii\base\Exception;

/**
 * Class NotificationEmail.
 *
 * @package app\components\notification
 */
class NotificationEmail extends NotifyFactory
{
    public function send()
    {
        if (empty($this->userId) || !($user = Users::findOne($this->userId)) || !$user->hasEmail()) {
            return false;
        }
        try {
            \Yii::$app->mailer->compose()
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->setTo($user->email)
                ->setSubject('Уведомление от GNS')
                ->setHtmlBody($this->message)
                ->send();
        } catch (\Exception $e) {
            var_export($e->getMessage());
        }
    }
}
