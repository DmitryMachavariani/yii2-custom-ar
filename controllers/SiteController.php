<?php

namespace app\controllers;

use app\components\BaseController;
use app\components\notification\NotifyFactory;
use app\models\notifications\Notification;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\LoginForm;

class SiteController extends BaseController
{
    public $defaultAction = 'login';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['tasks/my-tasks']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['']);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionTest()
    {
        $user = \app\models\Users::find()
            ->alias('u')
            ->joinWith('profile p')
            ->where(['u.username' => 'admin'])
            ->one();

        \yii\helpers\VarDumper::dump($user, 10, true);


        die;
    }
}
