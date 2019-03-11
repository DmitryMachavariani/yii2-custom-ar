<?php
/**
 * @var $this \yii\web\View
 */

\app\assets\GanttAsset::register($this);
$this->title = 'Гант';
$this->params['breadcrumbs'][] = ['label' => 'Диаграмма ганта', 'url' => ['gantt']];
?>
<div id="wrapper">
    <h2><?=$this->title?></h2>
    <div class="form-group">
        <label><input type="radio" name="scale" value="hour" class="form-controls" />Часы</label>
        <label><input type="radio" name="scale" value="day" class="form-controls" checked="checked" />Дни</label>
        <label><input type="radio" name="scale" value="week" class="form-controls"/>Недели</label>
        <label><input type="radio" name="scale" value="month" class="form-controls"/>Месяц</label>
        <label><input type="radio" name="scale" value="year" class="form-controls" />Год</label>
    </div>
    <div id="gantt-wrapper" class="margin-bottom"></div>
    <button id="gantt-more" class="btn btn-primary">Загрузить еще</button>
    <input id="gantt-current-page" type="hidden" value="1"/>
    <button id="zoom-to-fit" onclick="toggleMode(this)" class="btn btn-primary">Масштаб</button>
</div>
<div id="loader" style="display: none">
    <img src="/img/loader.gif" />
</div>
