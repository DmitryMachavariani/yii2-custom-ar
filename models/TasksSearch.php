<?php

namespace app\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;

/**
 * Class TasksSearch.
 *
 * @package app\models
 */
class TasksSearch extends Tasks
{
    public function rules()
    {
        return [
            [['id', 'project_id', 'status', 'priority', 'assigned_to', 'created_by', 'notify'], 'integer'],
            [['title', 'description', 'date_created', 'date_updated', 'project'], 'safe'],
            [['estimate'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array    $params
     *
     * @param int|null $projectId
     *
     * @return ActiveDataProvider
     */
    public function searchGrid(array $params, int $projectId = null)
    {
        $query = Tasks::find()
            ->withAllRelation()
            ->my();

        if ($projectId) {
            $query->byProject($projectId);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'pagination' => false,
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['project'] = [
            'asc' => ['projects.title' => SORT_ASC],
            'desc' => ['projects.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'estimate' => $this->estimate,
            'notify' => $this->notify,
            'date_created' => $this->date_created,
            'date_updated' => $this->date_updated,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'projects.title', $this->project]);

        return $dataProvider;
    }
}
