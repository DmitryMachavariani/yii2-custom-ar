<?php

$data = [
    'class' => app\components\Bot\Bot::class,
    'token' => '551578521:AAGzW24Qyi-GO_K3LLFU57r72ubrfX-J9p8',
    'usePassword' => false,
    'cacheTime' => 24 * 60 * 60, // minutes
    'verifySsl' => false,
    'proxyUrl' => '146.185.252.123:7358',
    'proxyAuth' => 'user16120:nwbhpy',
    'viewPath' => '@app/views/notifications/telegram'
];

if (file_exists(dirname(__FILE__) . '/local.bot.php')) {
    $data = \yii\helpers\ArrayHelper::merge($data, require (dirname(__FILE__) . '/local.bot.php'));
}

return $data;