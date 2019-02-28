<?php

$data = [
    'class' => yii\swiftmailer\Mailer::class,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'localhost',
        'username' => 'username',
        'password' => 'password',
        'port' => '111',
    ],
    'viewPath' => '@app/views/notifications/email'
];

if (file_exists(dirname(__FILE__) . '/local.mailer.php')) {
    $data = \yii\helpers\ArrayHelper::merge($data, require (dirname(__FILE__) . '/local.mailer.php'));
}

return $data;