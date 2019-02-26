<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form', 'class' => 'form-group has-feedback', 'fieldConfig' => [
        'template' => "{label}\n<div class=\"form-group has-feedback\">{input}</div>\n{error}",
    ],
]); ?>

<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

<?= $form->field($model, 'password')->passwordInput() ?>

<div class="row">
    <div class="col-xs-8">
        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"icheckbox_square-blue\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?>
    </div>

    <div class="col-xs-4">
        <?= Html::submitButton('Вход', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
    </div>
</div>

<a href="#">Забыл пароль</a>

<?php ActiveForm::end(); ?>