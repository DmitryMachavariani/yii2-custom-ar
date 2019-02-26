<?php

use yii\widgets\ListView;

/**
 *
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \yii\web\View
 */

echo ListView::widget([
    'summary' => false,
    'dataProvider' => $dataProvider,
    'itemView' => 'parts/one-task'
]);