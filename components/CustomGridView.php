<?php
namespace app\components;

use yii\grid\GridView;

class CustomGridView extends GridView
{
    public $layout = "{items}\n{pager}";

    public $tableOptions = [
        'class' => 'table table-hover'
    ];

    public $options = [
        'tag' => false
    ];
}