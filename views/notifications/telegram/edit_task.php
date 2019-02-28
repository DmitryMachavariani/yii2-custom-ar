Привет, <?=$user->username . PHP_EOL?>
Произошли изменения по задаче: <?=$task->title?> (<?=Yii::$app->params['baseUrl']?>tasks/task?taskId=<?=$task->id?>). <?=PHP_EOL?>
###
<?=mb_strcut(strip_tags($task->description), 0, 30)?>...
###
<?=PHP_EOL?>