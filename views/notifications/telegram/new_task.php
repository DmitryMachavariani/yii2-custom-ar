Привет, <?=$user->username . PHP_EOL?>
У тебя новая задача: <?=$task->title?> (<?=Yii::$app->params['baseUrl']?>tasks/task?taskId=<?=$task->id?>). <?=PHP_EOL?>
###
<?=mb_strcut(strip_tags($task->description), 0, 30)?>...
###
<?=PHP_EOL?>
<?php if ($task->planned_start_date): ?>
Тебе ее нужно начать <?=$task->planned_start_date?>.<?=PHP_EOL?>
<?php endif ?>

<?php if ($task->planned_end_date): ?>
Тебе ее нужно закончить <?=$task->planned_end_date?>.<?=PHP_EOL?>
<?php endif ?>