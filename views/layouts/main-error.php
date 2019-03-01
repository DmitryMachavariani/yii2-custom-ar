<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);
$this->registerCssFile(Yii::getAlias('@web/css/rewrite-theme.css'), [
    'depends' => \dmstr\web\AdminLteAsset::class
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition login-page">
<?php $this->beginBody() ?>
<div class="content">

    <div class="login-logo">
        <b>DEV-GNS</b>
    </div>

    <div class="">
        <?= $content ?>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
