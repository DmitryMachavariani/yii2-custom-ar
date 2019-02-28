<?php

namespace app\components\notification;

use app\models\Users;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class NotificationEmail.
 *
 * @package app\components\notification
 */
class NotificationEmail extends NotifyFactory
{
    public function send()
    {
        if (
            empty($this->userId) ||
            !($user = Users::findOne($this->userId)) ||
            !$user->hasEmail()
        ) {
            return false;
        }
        $this->params = ArrayHelper::merge($this->params, ['user' => $user, 'task' => $this->task]);
        $subject = $this->params['subject'] ?? 'Уведомление от GNS';
        $view = $this->params['view'] ?? null;
        try {
            $mailer = \Yii::$app->mailer->compose($view, $this->params)
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->setTo($user->email)
                ->setSubject($subject);
            if (!$view) {
                $mailer = $mailer->setHtmlBody($this->message);
            }
            $mailer->send();
        } catch (\Exception $e) {
            var_export($e->getMessage());
        }
    }
}
