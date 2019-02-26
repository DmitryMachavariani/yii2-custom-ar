<?php

$data = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

if (file_exists(dirname(__FILE__) . '/local.db.php')) {
    $data = \yii\helpers\ArrayHelper::merge($data, require (dirname(__FILE__) . '/local.db.php'));
}

return $data;