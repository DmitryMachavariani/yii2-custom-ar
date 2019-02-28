<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class GanttAsset.
 *
 * @package app\assets
 */
class GanttAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $css = [
        'js/vendor/gantt/dhtmlxgantt.css',
    ];

    public $js = [
        'js/gantt.js',
        'js/vendor/gantt/dhtmlxgantt.js',
        'js/vendor/gantt/locale/locale_ru.js',
        'js/vendor/gantt/ext/dhtmlxgantt_fullscreen.js',
        'js/vendor/gantt/ext/dhtmlxgantt_marker.js',
    ];

    public $depends = [
        CustomAsset::class
    ];
}
