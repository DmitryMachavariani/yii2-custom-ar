<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tasks]].
 *
 * @see Tasks
 */
class TasksQuery extends \yii\db\ActiveQuery
{
    public function withAllRelation()
    {
        return $this->joinWith([
            'created c',
            'assigned a',
            'project p',
            'history h',
            'trackers tr'
        ]);
    }

    /**
     * @return TasksQuery
     */
    public function my(): TasksQuery
    {
        return $this->andWhere(['assigned_to' => \Yii::$app->user->id]);
    }

    public function iCanSee()
    {
        return $this
            ->joinWith('members m')
            ->andWhere([
                'm.id' => \Yii::$app->user->id
            ])
            ->orWhere(['assigned_to' => \Yii::$app->user->id]);
    }

    /**
     * @param int $userId
     *
     * @return TasksQuery
     */
    public function foreign(int $userId): TasksQuery
    {
        return $this->andWhere(['assigned_to' => $userId]);
    }

    /**
     * @param int|null $projectId
     *
     * @return TasksQuery
     */
    public function byProject(int $projectId = null): TasksQuery
    {
        return $projectId ? $this->andWhere(['project_id' => $projectId]) : $this;
    }

    /**
     * {@inheritdoc}
     * @return Tasks[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Tasks|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
