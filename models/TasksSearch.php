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
    const OPEN_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_FEEDBACK,
        self::STATUS_FINISHED,
    ];

    /**
     * @var string
     */
    public $projectTitle;

    public function rules()
    {
        return [
            [['id', 'project_id', 'status', 'priority', 'assigned_to', 'created_by', 'notify'], 'integer'],
            [['title', 'description', 'date_created', 'date_updated', 'project'], 'safe'],
            [['projectTitle'], 'string', 'max' => 255],
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
     * @param bool     $onlyMyTasks
     *
     * @return ActiveDataProvider
     */
    public function searchGrid(array $params, int $projectId = null, $onlyMyTasks = false)
    {
        $query = Tasks::find()
            ->withAllRelation();
        if ($onlyMyTasks) {
            $query = $query->my();
        } else {
            $query = $query->iCanSee();
        }

        if ($projectId) {
            $query->byProject($projectId);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'pagination' => false,
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['projectTitle'] = [
            'asc' => ['p.title' => SORT_ASC],
            'desc' => ['p.title' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $status = $this->status ? $this->status : self::OPEN_STATUSES;

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.project_id' => $this->project_id,
            self::tableName() . '.status' => $status,
            self::tableName() . '.priority' => $this->priority,
            self::tableName() . '.assigned_to' => $this->assigned_to,
            self::tableName() . '.created_by' => $this->created_by,
            self::tableName() . '.estimate' => $this->estimate,
            self::tableName() . '.notify' => $this->notify,
            self::tableName() . '.date_created' => $this->date_created,
            self::tableName() . '.date_updated' => $this->date_updated,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', self::tableName() . '.description', $this->description])
            ->andFilterWhere(['like', 'p.title', $this->projectTitle]);

        return $dataProvider;
    }
}
