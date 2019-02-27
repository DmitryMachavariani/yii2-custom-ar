<?php

namespace app\models;

use yii\data\ActiveDataProvider;

/**
 * Class TasksSearch.
 *
 * @package app\models
 */
class TasksSearch extends Tasks
{
    /**
     * @param $userQuery
     *
     * @return ActiveDataProvider
     */
    public static function search($userQuery)
    {
        return new ActiveDataProvider([
            'sort' => false,
            'query' => Tasks::find()
                ->alias('t')
                ->withAllRelation()
                ->orWhere([
                    'LIKE',
                    't.title',
                    $userQuery
                ])
                ->orWhere([
                    'LIKE',
                    't.description',
                    $userQuery
                ])
                ->orWhere([
                    'LIKE',
                    'p.title',
                    $userQuery
                ])
                ->orderBy(['h.id' => SORT_DESC])
        ]);
    }
}
