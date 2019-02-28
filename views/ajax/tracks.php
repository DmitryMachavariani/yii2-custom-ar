<?php

use app\models\Trackers;
use yii\helpers\Url;

/**
 * @var $trackers \app\models\Trackers[]
 */
?>


<table class="table table-responsive">
    <tbody>

    <tr>
        <th>Действие</th>
        <th>Пользователь</th>
        <th>Время</th>
        <th>Дата</th>
        <th>Комментарий</th>
    </tr>

    <?php foreach ($trackers as $tracker): ?>
        <tr>
            <td><a href="#" class="fa fa-trash text-red" data-url="<?= Url::to(['ajax/remove-track', 'trackId' => $tracker->id]) ?>"></a> <span class="label label-success"><?= Trackers::getType($tracker->action) ?></span></td>
            <td><?= $tracker->user->profile->fullName ?></td>
            <td><?= $tracker->time ?> ч.</td>
            <td><?= date('Y-m-d', strtotime($tracker->date)) ?></td>
            <td><?= $tracker->comment ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>