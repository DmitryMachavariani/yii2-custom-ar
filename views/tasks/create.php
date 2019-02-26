<?php
use app\models\Tasks;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;

/**
 * @var $model \app\models\Tasks
 * @var $users array
 * @var $projects array
 * @var $this \yii\web\View
 */

$this->title = 'Создать новую задачу';
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'title') ?>

<?php if (empty($model->project_id)): ?>
    <?= $form->field($model, 'project_id')->dropDownList($projects) ?>
<?php endif; ?>

<?= $form->field($model, 'description')->widget(CKEditor::class, [
    'options' => ['rows' => 6],
    'preset' => 'basic'
]) ?>
<?= $form->field($model, 'status')->dropDownList(Tasks::STATUSES) ?>
<?= $form->field($model, 'priority')->dropDownList(Tasks::PRIORITIES) ?>
<?= $form->field($model, 'assigned_to')->dropDownList($users) ?>
<?= $form->field($model, 'estimate') ?>
<?= $form->field($model, 'notify')->dropDownList($users, [
    'prompt' => 'Выбрать пользователя'
]) ?>

<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
