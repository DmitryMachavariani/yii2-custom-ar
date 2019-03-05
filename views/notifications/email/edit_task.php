<p>Привет, <?=$user->username?>!</p>
Произошли изменения по задаче: <a href="<?=Yii::$app->params['baseUrl']?>/tasks/task?taskId=<?=$task->id?>"><?=$task->title?></a>. <br />
<pre>
    <?=$task->lastHistory?>
</pre>
<br />
