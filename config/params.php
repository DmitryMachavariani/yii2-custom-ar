<?php
Yii::setAlias('@uploads', dirname(__FILE__, 2) . '/web/uploads');
Yii::setAlias('@thumbs', dirname(__FILE__, 2) . '/web/thumbs');

$data = [
    'adminEmail' => 'info@dev-gns.com',
    'baseUrl' => 'https://taskmgr.dev-gns.com/',
];

if (file_exists(dirname(__FILE__) . '/local.params.php')) {
    $data = \yii\helpers\ArrayHelper::merge($data, require (dirname(__FILE__) . '/local.params.php'));
}

return $data;
