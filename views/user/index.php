<?php

use \app\components\CustomGridView;
use app\models\Users;

/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $this \yii\web\View
 */

$this->title = 'Управление пользователем';
?>
<h2><?=$this->title?></h2>
<div class="margin-bottom">
    <a href="/user/create" class="btn btn-success">Добавить</a>
</div>
<div class="box">
    <div class="box-body table-responsive no-padding">
        <?= CustomGridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'username',
                'email',
                [
                    'attribute' => 'status',
                    'value' => function (Users $user) {
                        return Users::getStatus($user->roleUser->role->name);
                    }
                ],
                'profile.fullName',
                [
                    'header' => 'Действия',
                    'class' => '\yii\grid\ActionColumn',
                    'template' => '{update}&nbsp;&nbsp;&nbsp;{delete}'
                ]
            ]
        ]) ?>
    </div>
</div>