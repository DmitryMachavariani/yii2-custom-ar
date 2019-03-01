<?php

namespace app\components;

use app\models\notifications\Notification;
use yii\filters\AccessControl;
use yii\web\Controller;

class BaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'ruleConfig' => [
                    'class' => CustomAccessRule::class
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?']
                    ],
                ],
            ],
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