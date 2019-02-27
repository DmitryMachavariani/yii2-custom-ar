<?php

use yii\helpers\Html;

?>

<div class="form-group">
    <label>Время в часах</label>
    <?= Html::textInput('time', null, ['class' => 'form-control']); ?>
</div>

<div class="form-group">
    <label>Тип работы</label>
    <?= Html::dropDownList('action', null, $actions, ['class' => 'form-control']); ?>
</div>

<div class="form-group">
    <label>Комментарий</label>
    <?= Html::textInput('comment', null, ['class' => 'form-control']); ?>
</div>

<?= Html::hiddenInput('taskId', $taskId); ?>
