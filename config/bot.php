<?php

$data = [
    'class' => app\components\Bot\Bot::class,
    'token' => '697551165:AAElBWtrSf6WXNeRGFw4uqRlZz_VZq-emtQ',
    'usePassword' => false,
    'objectsLimit' => 50,
    'cacheTime' => 24 * 60 * 60, // minutes
    'verifySsl' => false,
    'proxyUrl' => '146.185.252.123:7358',
    'proxyAuth' => 'user16120:nwbhpy',
    'messageSymbolsLimit' => 4000,
    'notifyTask' => 3 * 24 * 60 * 60
];

if (file_exists(dirname(__FILE__) . '/local.bot.php')) {
    $data = \yii\helpers\ArrayHelper::merge($data, require (dirname(__FILE__) . '/local.bot.php'));
}

return $data;