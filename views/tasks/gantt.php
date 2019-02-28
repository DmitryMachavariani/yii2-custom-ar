<?php
/**
 * @var $this \yii\web\View
 */

use app\models\GanttForm;
use yii\bootstrap\Html;

\app\assets\GanttAsset::register($this);
$this->title = 'Гант';
$this->params['breadcrumbs'][] = ['label' => 'Диаграмма ганта', 'url' => ['index']];
?>
    <div id="wrapper">
        <div class="form-group">
            <label><input type="radio" name="scale" value="day" class="form-control"/>Дни</label>
            <label><input type="radio" name="scale" value="week" class="form-control"/>Недели</label>
            <label><input type="radio" name="scale" value="month" class="form-control"/>Месяц</label>
            <label><input type="radio" name="scale" value="year" class="form-control" checked="checked" />Год</label>
        </div>
        <div id="gantt-wrapper"></div>
        <button id="zoom-to-fit" onclick="toggleMode(this)" class="btn btn-primary">Масштаб</button>
    </div>
<div id="loader" style="display: none">
    <img src="/img/loader.gif" />
</div>
<!--<input type="hidden" id="current-user-id" value="--><?//=(!\Yii::$app->user->identity-> ? \Yii::$app->user->id : '')?><!--"/>-->

