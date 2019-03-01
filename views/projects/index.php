<?php

use app\components\CustomGridView;
use app\models\Projects;
use yii\helpers\Html;

/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this         \yii\web\View
 */

$this->title = 'Проекты';
?>

<?= Html::a('Создать проект', ['projects/create'], ['class' => 'btn btn-success']) ?>

<br>
<br>

<div class="box">
    <div class="box-body table-responsive no-padding">
        <?= CustomGridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'id',
                    'format' => 'raw',
                    'value' => function (Projects $model) {
                        return Html::a($model->id, ['tasks/tasks', 'projectId' => $model->id]);
                    }
                ],
                'title',
                [
                     'header' => 'Прогресс',
                    'format' => 'raw',
                    'value' => function (Projects $model) {
                        $percent = $model->percentComplete * 100;

                        return "
                        <div class='progress progress-xs'>
                            <div class='progress-bar progress-bar-aqua' style='width: {$percent}%'></div>
                        </div>";
                    }
                ],
                'date_created',
                [
                    'header' => 'Действия',
                    'class' => '\yii\grid\ActionColumn',
                    'template' => '{update}&nbsp;&nbsp;&nbsp;{delete}'
                ]
            ]
        ]) ?>
    </div>
</div>