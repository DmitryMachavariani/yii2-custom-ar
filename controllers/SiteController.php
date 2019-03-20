<?php

namespace app\controllers;

use app\components\BaseController;
use app\components\Bot\Curl;
use app\models\SendMessageForm;
use app\models\User;
use app\models\Users;
use Yii;
use yii\web\Response;
use app\models\LoginForm;

class SiteController extends BaseController
{
    public $defaultAction = 'login';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $parent = parent::behaviors();
        $rules = [
            [
                'roles' => [Users::STATUS_USER],
                'actions' => ['short'],
                'allow' => true
            ],
            [
                'roles' => [Users::STATUS_ADMIN],
                'actions' => ['test'],
                'allow' => false
            ],
        ];

        $result = array_merge($parent['access']['rules'], $rules);
        $parent['access']['rules'] = $result;

        return $parent;
    }

    public function actionLong()
    {
        $t = time();

        if (Yii::$app->user->can('canLong')) {
            echo 'Privet <br>';
        }

        echo $t;
    }

    public function actionShort()
    {
        $t = 'Short';

        if (Yii::$app->user->can('canDo')) {
            echo 'CAN DO!!!!!!1 <br>';
        }

        echo $t;
    }

    public function actionTest()
    {
        $t = 'Test';

        if (Yii::$app->user->can('canDo')) {
            echo 'CAN DO!!!!!!1 <br>';
        }

        echo $t;
    }

    public function actionError()
    {
        $this->layout = '@app/views/layouts/main-error';

        $exception = Yii::$app->errorHandler->exception;

        $message = $exception->getMessage();
        $code = Yii::$app->response->getStatusCode();

        if ($exception !== null) {
            return $this->render('error', compact('message', 'code'));
        }
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
        $log = '------------' .
            PHP_EOL .
            date('d.m.Y H:i:s') . PHP_EOL .
            $input . PHP_EOL;
        file_put_contents(dirname(__FILE__, 2) . '/runtime/test_hook2.log', $log, FILE_APPEND);

        $data = json_decode($input, 1);
        Yii::$app->bot->run($data);
    }

    public function actionSendMessage()
    {
        $users = User::find()->all();
        $model = new SendMessageForm();

        if ($model->load(Yii::$app->request->post()) && $model->send()) {
            Yii::$app->session->setFlash('success', 'Ok');
            return $this->refresh();
        }

        return $this->render('send-message', compact('model', 'users'));
    }
}
