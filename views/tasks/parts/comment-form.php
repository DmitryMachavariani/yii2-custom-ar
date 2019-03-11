<?php
/**
 * @var $commentModel \app\models\TaskComment
 * @var $model \app\models\Tasks
 */

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>

<?php $form = ActiveForm::begin([
    'id' => 'save-comment-form',
    'action' => '/ajax/add-comment?commentId=' . $commentModel->id,
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnBlur' => false,
    'validateOnSubmit' => false,
]) ?>
<?=$form->field($commentModel, 'task_id')->hiddenInput(['value' => $model->id])->label(false)?>
<?=$form->field($commentModel, 'id')->hiddenInput(['value' => $commentModel->id])->label(false)?>
<?=$form->field($commentModel, 'author_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false)?>

<?= $form->field($commentModel, 'text')->widget(CKEditor::class, [
    'options' => ['rows' => 6],
    'preset' => 'basic'
])->label('Добавить комментарий');?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>
