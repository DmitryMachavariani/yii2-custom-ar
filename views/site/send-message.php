<?php

use dosamigos\ckeditor\CKEditor;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @var $this         \yii\web\View
 * @var $model        \app\models\SendMessageForm
 */
$this->title = 'Отправить сообщение';
?>

    <h2><?=$this->title?></h2>
<?php $form = ActiveForm::begin() ?>

<?= $form->errorSummary($model) ?>
<?=Html::checkbox(null, false, [
    'label' => 'Check all',
    'class' => 'check-all',
]);?>
<?=$form->field($model, 'userIds')->checkboxList(
    ArrayHelper::map($users, 'id', 'username'),
    ['separator' => '<br>']
)?>

<?= $form->field($model, 'message')->widget(CKEditor::class, [
    'options' => ['rows' => 6],
    'preset' => 'basic'
]) ?>

<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
