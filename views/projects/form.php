<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;

/**
 * @var $model \app\models\Tasks
 * @var $users array
 * @var $projects array
 * @var $this \yii\web\View
 */

$this->title = 'Создание проекта';
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'description')->widget(CKEditor::class, [
    'options' => ['rows' => 6],
    'preset' => 'basic'
]) ?>

<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
