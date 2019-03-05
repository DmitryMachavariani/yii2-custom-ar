<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Projects;
use app\models\ProjectUser;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ProjectsController extends BaseController
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'sort' => false,
            'query' => Projects::find()
                ->alias('p')
                ->addSelect('p.*')
                ->percentOfCompleteTasks()
                ->withTasks()
                ->groupBy('p.id')
        ]);

        return $this->render('index', compact('dataProvider'));
    }

    public function actionCreate()
    {
        $model = new Projects();
        $members = Users::find()->fioAndPositionAsUsername()->all();
        $membersModel = new ProjectUser();

        $modelLoad = $model->load(\Yii::$app->request->post());

        if ($modelLoad && $model->save()) {
            $membersModel->load(\Yii::$app->request->post());
            $model->addMembers($membersModel->user_id);
            \Yii::$app->session->setFlash('success', 'Проект успешно создан');
            return $this->redirect(['projects/index']);
        }

        return $this->render('form', compact('model', 'members'));
    }

    /**
     * @param int $id
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = Projects::find()->where(['id' => $id])->one();
        $members = Users::find()->fioAndPositionAsUsername()->all();
        $membersModel = new ProjectUser();

        if (!$model) {
            throw new NotFoundHttpException("Проект не найден");
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $membersModel->load(\Yii::$app->request->post());
            $model->addMembers($membersModel->user_id);
            \Yii::$app->session->setFlash('success', 'Проект успешно обновлен');
            return $this->redirect(['projects/update?id=' . $id]);
        }

        return $this->render('form', compact('model', 'members'));
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
     * @return Projects the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Projects::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}