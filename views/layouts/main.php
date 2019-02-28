<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */


\app\assets\AdminAsset::register($this);
\app\assets\CustomAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$modalButtons = [
    Html::button('Закрыть', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']),
    Html::button('Сохранить изменения', ['class' => 'btn btn-primary pull-left', 'id' => 'save-changes-modal'])
];

$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Url::to(['/favicon.ico'])]);
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
<body class="hold-transition skin-purple sidebar-mini layout-boxed">
<?php $this->beginBody() ?>
<div class="wrapper">

    <?php
    Modal::begin([
        'header' => 'Подтверждение действия',
        'id' => 'modal-default',
        'size' => 'modal-md',
        'options' => [
            'class' => 'modal fade in'
        ],
        'footer' => implode(' ', $modalButtons),
    ]);

    echo Html::tag('div', null, ['id' => 'alert-js-modal']);
    echo Html::tag('div', '', ['id' => 'modalContent']);

    Modal::end();
    ?>

    <?= $this->render(
        'header',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left',
        ['directoryAsset' => $directoryAsset]
    )
    ?>

    <?= $this->render(
        'content',
        ['content' => $content]
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>