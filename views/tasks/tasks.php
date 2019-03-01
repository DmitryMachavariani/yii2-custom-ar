<?php

use app\components\CustomGridView;
use app\models\Tasks;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 *
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\models\TasksSearch
 * @var $projectId int
 * @var $statuses array
 * @var $this \yii\web\View
 */

$this->title = ($myTasks ? 'Мои задачи' : 'Задачи') . ($projectId ? ' по проекту ' . \app\models\Projects::findOne($projectId) ->title : '');
?>

<h2><?=$this->title?></h2>

<div class="margin-bottom">
    <?php if ($myTasks): ?>
        <?= Html::a('Все задачи', ['tasks/tasks'], ['class' => 'btn btn-success']) ?>&nbsp;
    <?php else: ?>
        <?= Html::a('Мои задачи', ['tasks/my-tasks'], ['class' => 'btn btn-success']) ?>&nbsp;
    <?php endif; ?>
    <?= Html::a('Завести задачу', ['tasks/create', 'projectId' => $projectId], ['class' => 'btn btn-success']) ?>
</div>

<div class="box">
    <div class="box-body table-responsive no-padding">
    <?= CustomGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
                'filter' => false,
                'value' => function (Tasks $task) {
                    return Html::a($task->title, ['tasks/task', 'taskId' => $task->id]);
                }
            ],
            [
                'attribute' => 'project.title',
                'format' => 'raw',
                'value' => function (Tasks $task) {
                    return Html::a($task->project->title, ['tasks/tasks', 'projectId' => $task->project_id]);
                }
            ],
            [
                'attribute' => 'status',
                'filter' => $statuses,
                'value' => function (Tasks $task) {
                    return Tasks::getStatus($task->status);
                }
            ],
            [
                'attribute' => 'priority',
                'format' => 'raw',
                'value' => function (Tasks $task) {
                    return Tasks::getPriorityColored($task->priority);
                }
            ],
            [
                'attribute' => 'estimate',
                'value' => function (Tasks $task) {
                    return $task->estimate . ' ч.';
                }
            ],
            [
                'attribute' => 'tracked',
                'format' => 'raw',
                'value' => function (Tasks $task) {
                    $span = Html::tag('span', null, ['class' => 'fa fa-clock-o']);
                    $url = Url::to(['ajax/track']);
                    return $task->tracked . ' ч. ' .  Html::a($span, '#', ['data-url' => $url, 'data-id' => $task->id]);
                }
            ],
            [
                'attribute' => 'date_created',
                'format' => 'raw',
                'filter' => \kartik\date\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_created',
                    'pickerButton' => false,
                    'pickerIcon' => false,
                    'value' => $searchModel->date_created,
                    'pluginOptions' => [
                         'format' => 'yyyy-mm-dd',
                         'todayHighlight' => true
                    ]
                ]),
                'value' => function (Tasks $task) {
                    return date('Y-m-d', strtotime($task->date_created));
                }
            ],
        ]
    ]) ?>
    </div>
</div>