<p>Привет, <?=$user->username?>!</p>
<p>Доступ к GNS таск-менеджеру:</p>
<ul>
    <li>URL: <a href="<?=Yii::$app->params['baseUrl']?>"><?=Yii::$app->params['baseUrl']?></a></li>
    <li>Логин: <?=$user->username?></li>
    <li>Пароль: <?=$password?></li>
</ul>
