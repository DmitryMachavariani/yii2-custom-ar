<?php

use app\models\Tasks;
use app\models\History;

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
                <small class="pull-right">Дата создания: <?= date('Y-m-d', strtotime($model->date_created)) ?></small>
            </h2>
        </div>
        <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            ID <strong><?= $model->id ?></strong><br>
            Создал <strong><?= $model->created->profile->fullName ?></strong><br>
            Статус <strong><?= Tasks::getStatus($model->status) ?></strong> <a href="#" id="change-status" class="fa fa-edit text-blue" data-id="<?= $model->id ?>"></a><br>
        </div>
        <!-- /.col -->

        <div class="col-sm-4 invoice-col">
            Назначена <strong><?= $model->assigned->profile->fullName ?></strong>  <a href="#" class="fa fa-edit text-blue" data-id="<?= $model->id ?>" id="change-assigned"></a><br>
            Оценка трудозатрат <strong><?= $model->estimate ?></strong> ч.<br>
            Приоритет <strong><?= Tasks::getPriorityColored($model->priority) ?></strong><br>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <br>

    <?php if (!empty($model->description)): ?>
        Описание
        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
            <?= $model->description ?>
        </p>
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

                            <h3 class="timeline-header"><a href="#"><?= History::getType($history->type) ?></a></h3>

                            <div class="timeline-body">
                                <?= $history->comment ?>
                            </div>
                        </div>
                    </li>
                    <!-- END timeline item -->
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- /.row -->
    <?php endif; ?>

    <!-- this row will not appear when printing -->
    <div class="row no-print">
        <div class="col-xs-12">
            <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
            <button type="button" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment
            </button>
            <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
                <i class="fa fa-edit"></i> Изменить
            </button>
        </div>
    </div>
</section>
