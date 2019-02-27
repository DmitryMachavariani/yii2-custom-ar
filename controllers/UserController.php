<?php
namespace app\controllers;

use app\components\BaseController;
use app\models\Settings;
use app\models\Users;
use yii\data\ActiveDataProvider;
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
//                var_export($settingsModel->getAttributes()); die;
                $settingsModel->saveData();
            }

            \Yii::$app->session->setFlash('success', 'Пользователь успешно сохранен');
            return $this->redirect(['user/index']);
        }


        return $this->render('form', compact('model', 'settingsModel'));
    }
}