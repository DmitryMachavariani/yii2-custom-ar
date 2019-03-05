Привет, <?=$user->username . PHP_EOL?>
Произошли изменения по задаче: <?=$task->title?> (<?=Yii::$app->params['baseUrl']?>tasks/task?taskId=<?=$task->id?>). <?=PHP_EOL?>
###
<?=$task->lastHistory . PHP_EOL?>
###
<?=PHP_EOL?>