<?php
/**
 * @var $model \app\models\Tasks
 */
?>

<h3>Комментарии к задаче <?=$model->title?></h3>
<div class="row">
    <div class="timeline comments">
        <?php foreach ($model->comments as $comment): ?>
                <div class="time-label bg-gray">
                    <span class="bg-purple">
                        <?= date('Y-m-d', strtotime($comment->date_updated)) ?>
                    </span>
                    <span class="pull-right bg-aqua">
                        <?= $comment->author->username ?>
                    </span>
                </div>
                <div class="timeline-item margin-bottom bg-gray">
                    <div class="timeline-body">
                        <?= nl2br($comment->text) ?>
                    </div>
                </div>
        <?php endforeach; ?>
    </div>
</div>
