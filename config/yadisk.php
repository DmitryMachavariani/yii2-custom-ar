<?php

$data = [
    'class' => \app\components\Storage::class,
    'token' => 'AQAAAAASG4GJAAWKs7d1FLgrhkE9p_J0VJfIxPo',
    'folder' => 'taskmgr',
    'localBaseFolder' => '@uploads'
];

if (file_exists(dirname(__FILE__) . '/local.yadisk.php')) {
    $data = \yii\helpers\ArrayHelper::merge($data, require (dirname(__FILE__) . '/local.yadisk.php'));
}

return $data;