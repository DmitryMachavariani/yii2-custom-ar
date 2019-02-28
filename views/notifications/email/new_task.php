<p>Привет, <?=$user->username?>!</p>
У тебя новая задача: <a href="<?=Yii::$app->params['baseUrl']?>/tasks/task?taskId=<?=$task->id?>"><?=$task->title?></a>. <br />
<pre>
<?=mb_strcut($task->description, 0, 100)?>...
</pre>
<br />
<?php if ($task->planned_start_date): ?>
    Тебе ее нужно начать <?=$task->planned_start_date?>.<br />
<?php endif ?>

<?php if ($task->planned_end_date): ?>
    Тебе ее нужно закончить <?=$task->planned_end_date?>.<br />
<?php endif ?>