Привет, <?=$user->username . PHP_EOL?>
Новая задача: <?=$task->title?> (<?=Yii::$app->params['baseUrl']?>tasks/task?taskId=<?=$task->id?>). <?=PHP_EOL?>.
Назначена на <?=$task->assigned->username . PHP_EOL?>
Посмотреть задачу <a href="/t<?=$task->id?>">/t<?=$task->id?></a><?=PHP_EOL?>
###
<?=mb_strcut(strip_tags($task->description), 0, 30)?>...
###
<?=PHP_EOL?>
<?php if ($task->planned_start_date): ?>
Дата начала: <?=$task->planned_start_date?>.<?=PHP_EOL?>
<?php endif ?>

<?php if ($task->planned_end_date): ?>
Дата окончания: <?=$task->planned_end_date?>.<?=PHP_EOL?>
<?php endif ?>