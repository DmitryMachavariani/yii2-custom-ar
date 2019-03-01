<?php

namespace app\controllers;

use app\components\BaseController;
use app\components\Bot\Curl;
use app\components\notification\NotifyFactory;
use app\models\notifications\Notification;
use app\models\Projects;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\LoginForm;

class SiteController extends BaseController
{
    public $defaultAction = 'login';
    public $enableCsrfValidation = false;

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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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

    public function actionSetHook()
    {
        $token = Yii::$app->bot->token;
        $callback = Yii::$app->params['baseUrl'];
        $url = "https://api.telegram.org/bot{$token}/setWebhook?url={$callback}site/bot";

        $curl = new Curl();
        $response = $curl->get($url);

        var_export([
            $url,
            $response->getContent()
        ]); die;
    }

    public function actionBot()
    {
        $this->enableCsrfValidation = false;
        $input = file_get_contents('php://input');

        $data = json_decode($input, 1);
        Yii::$app->bot->run($data);
    }

    public function actionTest()
    {
        //
    }

}
