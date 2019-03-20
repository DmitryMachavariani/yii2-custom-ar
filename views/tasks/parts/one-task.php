<?php

use app\models\Tasks;
use app\models\History;
use yii\helpers\Url;

/**
 * @var $model \app\models\Tasks
 * @var $this  \yii\web\View
 */

$this->title = "Задача: {$model->title}";
?>

<section class="invoice">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-globe"></i> <?= $model->title ?>
                <a href="<?= Url::to(['tasks/update', 'taskId' => $model->id]) ?>" type="button" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                    <i class="fa fa-edit"></i>
                </a>
                <small class="pull-right">Дата создания: <?= date('Y-m-d', strtotime($model->date_created)) ?></small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            Проект <a href="/tasks/tasks?projectId=<?=$model->project_id?>"><strong><?= $model->project->title ?></strong></a><br>
            ID <strong><?= $model->id ?></strong><br>
            Создал <strong><?= $model->created->profile->fullName ?></strong><br>
            Статус <strong><?= Tasks::getStatus($model->status) ?></strong> <a href="#" id="change-status" class="fa fa-edit text-blue" data-id="<?= $model->id ?>"></a><br>
        </div>
        <!-- /.col -->

        <div class="col-sm-4 invoice-col">
            Назначена <strong><?= $model->assigned->profile->fullName ?></strong>  <a href="#" class="fa fa-edit text-blue" data-id="<?= $model->id ?>" id="change-assigned"></a><br>
            Оценка трудозатрат <strong><?= $model->estimate ?></strong> ч.<br>
            Трудозатраты <strong><?= $model->tracked ?></strong> ч. <a href="#" class="fa fa-info-circle" title="Список треков" data-url="<?= Url::to(['ajax/tracks']) ?>" data-id="<?= $model->id ?>"></a> <br>
            Приоритет <strong><?= Tasks::getPriorityColored($model->priority) ?></strong><br>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <br>

    <?php if (!empty($model->description)): ?>
        Описание
        <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            <?= $model->description ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($model->history)): ?>
        <div class="row">
            <ul class="timeline">
                <?php foreach ($model->history as $history): ?>
                    <!-- timeline time label -->
                    <li class="time-label">
                        <span class="bg-purple">
                            <?= date('Y-m-d', strtotime($history->date)) ?>
                        </span>
                    </li>
                    <!-- /.timeline-label -->

                    <!-- timeline item -->
                    <li>
                        <!-- timeline icon -->
                        <i class="fa bg-warning <?= History::getIconByType($history->type) ?>"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fa fa-clock-o"></i> <?= date('G:i', strtotime($history->date)) ?></span>

                            <h3 class="timeline-header"><span class="label label-info"><?= History::getType($history->type) ?></span></h3>

                            <div class="timeline-body"><?= nl2br($history->comment) ?></div>
                        </div>
                    </li>
                    <!-- END timeline item -->
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- /.row -->
    <?php endif; ?>
    <div class="margin-bottom">
        <div class="row">
            <h3>Файлы:</h3>
            <?php if (!empty($model->attachments)): ?>
                <ul class="documents-list">
                    <?php foreach ($model->attachments as $file): ?>
                        <li>
                            <span class="glyphicon glyphicon-file"></span>
                            <a target="_blank" href="<?= $file->getInternalFileUrl() ?>"><?= $file->name ?></a> (<?= date('d.m.Y H:i', strtotime($file->date_updated)) ?>)
                            <a href="<?= Url::to(['tasks/download', 'file_id' => $file->id]) ?>"><span class="glyphicon glyphicon-file"></span></a>
                            <a href="<?= Url::to(['ajax/remove-file', 'file' => $file->id]) ?>" class="js-remove-document"><span class="glyphicon glyphicon-remove"></span></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <!-- /.row -->
            <?php endif; ?>
            <a href="<?= Url::to(['tasks/update', 'taskId' => $model->id]) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Добавить файлы</a>
        </div>
    </div>
    <div class="margin-bottom">
        <?php if (!empty($model->comments)): ?>
            <?=$this->render('comments-list', compact('model'));?>
        <?php else : ?>

        <?php endif; ?>
    </div>
</section>
