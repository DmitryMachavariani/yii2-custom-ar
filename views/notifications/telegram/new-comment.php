Привет, <?=$user->username . PHP_EOL?>
К задаче: <?=$task->title?> (<?=Yii::$app->params['baseUrl']?>tasks/task?taskId=<?=$task->id?>) был добавлен комментарий. <?=PHP_EOL?>
###
<?=strip_tags($comment). PHP_EOL?>
###
<?=PHP_EOL?>