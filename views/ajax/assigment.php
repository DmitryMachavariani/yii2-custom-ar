<?php

use yii\helpers\Html;

?>

<?= Html::dropDownList('userId', null,$users, ['class' => 'form-control']); ?>
<?= Html::hiddenInput('taskId', $taskId); ?>
