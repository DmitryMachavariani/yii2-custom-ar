<p>Привет, <?=$user->username?>!</p>
К задаче: <a href="<?=Yii::$app->params['baseUrl']?>/tasks/task?taskId=<?=$task->id?>"><?=$task->title?></a> был добавлен комментарий. <br />
<pre>
    <?=$comment?>
</pre>
<br />
