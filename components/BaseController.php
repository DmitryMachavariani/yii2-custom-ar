<?php

namespace app\components;

use app\models\notifications\Notification;
use vladayson\AccessRules\AccessControl;
use yii\web\Controller;
use app\models\Users;

class BaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => [Users::STATUS_ADMIN],
                        'allow' => true
                    ],
                ],
            ]
        ];
    }

    public function init()
    {
        $this->layout = '@app/views/layouts/main';

        if (!\Yii::$app->user->isGuest) {
            $notifications = Notification::find()
                ->withStatus(Notification::STATUS_IN_PROGRESS)
                ->my()
                ->inside()
                ->all();

            $this->view->params['countNotifications'] = count($notifications);
            $this->view->params['notifications'] = $notifications;
        }

        parent::init();
    }
}