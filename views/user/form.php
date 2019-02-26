<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Users;

/**
 * @var $this \yii\web\View
 * @var $model \app\models\Users
 */
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->errorSummary($model) ?>

<?= $form->field($model, 'email') ?>
<?= $form->field($model, 'status')->dropDownList(Users::STATUSES) ?>


<?= $form->field($model->profile, 'last_name') ?>
<?= $form->field($model->profile, 'first_name') ?>
<?= $form->field($model->profile, 'middle_name') ?>

<?php if (file_exists(Yii::getAlias('@webroot/uploads/') . $model->profile->photo)): ?>
    <img src="<?= Yii::$app->request->baseUrl ?>/uploads/<?= $model->profile->photo ?>" class="image" width="200">
    <br><br>
<?php endif; ?>

<?= $form->field($model->profile, 'photo')->fileInput() ?>


<?= $form->field($model->profile, 'job') ?>

<?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>
