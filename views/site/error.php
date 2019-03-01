<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $code . " ошибка";
?>

<section class="content">

    <div class="error-page">
        <h2 class="headline text-red"><?= $code ?></h2>

        <br>

        <div class="error-content">
            <h3><i class="fa fa-warning text-red"></i> Прошу прощения.</h3>

            <p><?= nl2br(Html::encode($message)) ?></p>

            <?= Html::a('На главную', ['site/login'], ['class' => 'btn btn-lg btn-primary']) ?>

        </div>
    </div>
    <!-- /.error-page -->
</section>

