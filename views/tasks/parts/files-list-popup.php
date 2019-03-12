<?php
yii\bootstrap\Modal::begin([
    'header' => '<h4>Выберите файл для вставки</h4>',
    'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
]);
?>
<div id="modalContent">
    <div class="margin-bottom">
    <?=\yii\helpers\Html::dropDownList(
            'files',
            null,
            \yii\helpers\ArrayHelper::map($files, 'id', 'name'),
            ['class' => 'form-control', 'id' => 'js-select-file']
    );?>
    </div>
    <?=\yii\helpers\Html::button('Вставить', ['class' => 'btn btn-primary js-insert-files']);?>
</div>
<?php yii\bootstrap\Modal::end(); ?>


