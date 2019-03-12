<?php

use app\models\Tasks;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;
use kartik\date\DatePicker;
use \yii\helpers\ArrayHelper;

/**
 * @var $model \app\models\Tasks
 * @var $commentModel \app\models\TaskComment
 * @var $users array
 * @var $projects array
 * @var $this \yii\web\View
 */

$this->title = $model->isNewRecord ? 'Создать новую задачу' : 'Обновить задачу';
$classes = [
    'form-group',
    'field-tasks-description',
];
?>

<h2><?=$this->title?></h2>

<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'title') ?>

<?php if ($model->isNewRecord) {
    $options = [
        'class' => $classes
    ];
} else {
    $options = [
        'class' => ArrayHelper::merge($classes, ['hidden-toggle'])
    ];
?>

<?php } ?>
<?= $form->field($model, 'description', [
        'options' => $options,
        'labelOptions' => [ 'class' => 'toggle-label']
])->widget(CKEditor::class, [
    'options' => ['rows' => 6],
    'preset' => 'basic'
]) ?>

<div class="container-clear">
    <div class="raw">
        <div class="col-md-6">
            <?php if (empty($model->project_id)): ?>
                <?= $form->field($model, 'project_id')->dropDownList($projects) ?>
            <?php endif; ?>
            <?= $form->field($model, 'status')->dropDownList(Tasks::STATUSES) ?>
            <?= $form->field($model, 'priority')->dropDownList(Tasks::PRIORITIES) ?>
            <?= $form->field($model, 'assigned_to')->dropDownList($users) ?>
            <?= $form->field($model, 'estimate') ?>
            <?= $form->field($model, 'notify')->dropDownList($users, [
                'prompt' => 'Выбрать пользователя'
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'files[]')->fileInput(['multiple' => true]) ?>

            <?= $form->field($model, 'planned_start_date')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>

            <?= $form->field($model, 'planned_end_date')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>

            <?= $form->field($model, 'real_start_date')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>

            <?= $form->field($model, 'real_end_date')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true
                ]
            ]) ?>
        </div>
    </div>
</div>

<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
<?php if (!$model->isNewRecord): ?>
    <a class="btn btn-primary" href="/tasks/task?taskId=<?=$model->id?>">Посмотреть задачу</a>
<?php endif; ?>

<?php ActiveForm::end() ?>

<?=$this->render('parts/comment-form', compact('model', 'commentModel'));?>
