<?php

use app\components\CustomGridView;
use app\models\Tasks;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 *
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $projectId int
 * @var $this \yii\web\View
 */

$this->title = 'Задачи';
?>


<?= Html::a('Завести задачу', ['tasks/create', 'projectId' => $projectId], ['class' => 'btn btn-success']) ?>

<br>
<br>

<div class="box">
    <div class="box-body table-responsive no-padding">
    <?= CustomGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'raw',
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
                'value' => function (Tasks $task) {
                    return date('Y-m-d', strtotime($task->date_created));
                }
            ],
        ]
    ]) ?>
    </div>
</div>