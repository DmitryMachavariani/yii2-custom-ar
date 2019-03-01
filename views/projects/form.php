<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;
use \yii\helpers\ArrayHelper;

/**
 * @var $model \app\models\Tasks
 * @var $users array
 * @var $projects array
 * @var $this \yii\web\View
 */

$this->title = 'Создание проекта';
?>

<?php $form = ActiveForm::begin() ?>
<div class="container-fluid">
    <div class="raw">
        <div class="col-md-6">
            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'description')->widget(CKEditor::class, [
                'options' => ['rows' => 6],
                'preset' => 'basic'
            ]) ?>
        </div>
        <div class="col-md-6">
            <label>Участники проекта</label>
            <div class="form-group list-members">
                <?=Html::checkboxList(
                        'ProjectUser[user_id][]',
                        ArrayHelper::map($model->members, 'id', 'id'),
                        ArrayHelper::map($members, 'id', 'username'),
                        [
                            'class' => 'form-control'
                        ]
                );?>
            </div>
        </div>
    </div>
</div>

<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
