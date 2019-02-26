<?php

use yii\helpers\Html;

?>

<?= Html::dropDownList('statuses', null,$statuses, ['class' => 'form-control']); ?>
<?= Html::hiddenInput('taskId', $taskId); ?>
