<?php
namespace app\controllers;

use app\components\BaseController;
use app\components\notification\NotifyFactory;
use app\models\notifications\Notification;
use app\models\Profile;
use app\models\Settings;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends BaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Users::find()->withProfile(),
            'sort' => false,
        ]);

        return $this->render('index', compact('dataProvider'));
    }

    /**
     * @param int|null $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionUpdate(int $id = null)
    {
        if ($id === null) {
            $id = \Yii::$app->user->id;
        }

        $settingsModel = new Settings();

        $model = Users::find()
            ->alias('u')
            ->where(['u.id' => $id])
            ->withProfile()
            ->one();

        $model->setScenario(Users::SCENARIO_UPDATE);

        $profile = $model->profile;

        $userLoad = $model->load(\Yii::$app->request->post());
        $profileLoad = $model->profile->load(\Yii::$app->request->post());

        if ($profileLoad && $userLoad && $model->save()) {
            $model->profile->photo = UploadedFile::getInstance($model->profile, 'photo');

            if ($model->profile->validate()) {
                if ($model->profile->photo) {
                    $name = \Yii::$app->security->generateRandomString(10);
                    $fullName = $name . '.' .$model->profile->photo->extension;
                    $path = \Yii::getAlias('@uploads/') . $fullName;

                    $model->profile->photo->saveAs($path);
                    $model->profile->photo = $fullName;
                }

                $model->profile->save();
            }
            $settingsModel->user_id = $model->id;
            if ($settingsModel->load(\Yii::$app->request->post())) {
                $settingsModel->saveData();
            }

            \Yii::$app->session->setFlash('success', 'Пользователь успешно сохранен');
            return $this->redirect(['user/index']);
        }


        return $this->render('form', compact('model', 'settingsModel', 'profile'));
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $settingsModel = new Settings();

        $model = new Users();
        $profile = new Profile();

        $userLoad = $model->load(\Yii::$app->request->post());
        $profileLoad = $profile->load(\Yii::$app->request->post());

        if ($profileLoad && $userLoad && $model->save()) {
            $password = $model->generatePassword();
            $profile->photo = UploadedFile::getInstance($profile, 'photo');
            $profile->user_id = $model->id;

            if ($profile->validate()) {
                if ($profile->photo) {
                    $name = \Yii::$app->security->generateRandomString(10);
                    $fullName = $name . '.' . $profile->photo->extension;
                    $path = \Yii::getAlias('@uploads/') . $fullName;

                    $profile->photo->saveAs($path);
                    $profile->photo = $fullName;
                }

                $profile->save();
            }
            $settingsModel->user_id = $model->id;
            if ($settingsModel->load(\Yii::$app->request->post())) {
                $settingsModel->saveData();
            }
            \Yii::$app->queue->push(NotifyFactory::create(Notification::TYPE_MAIL)
                ->setUserId($model->id)
                ->setParams([
                    'view' => 'register',
                    'password' => $password
                ]));
            ;

            \Yii::$app->session->setFlash('success', 'Пользователь успешно сохранен');
            return $this->redirect(['user/index']);
        }

        return $this->render('form', compact('model', 'settingsModel', 'profile'));
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->session->setFlash('success', 'Запись успешно удалена');

        return $this->redirect(\Yii::$app->request->getReferrer());
    }

    /**
     * Finds the Actions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}