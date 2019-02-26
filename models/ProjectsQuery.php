<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Projects]].
 *
 * @see Projects
 */
class ProjectsQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Projects[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Projects|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param        $status
     *
     * @param string $alias
     *
     * @return ProjectsQuery
     */
    public function withTasks($status = null, string $alias = 't'): ProjectsQuery
    {
        $this->joinWith([
            "tasks {$alias}" => function (ActiveQuery $query) use ($status, $alias) {
                if (empty($status) || is_null($status)) {
                    return $query;
                }

                return $query->andOnCondition(["{$alias}.status" => $status]);
            }
        ]);

        return $this;
    }

    /**
     * @return ProjectsQuery
     */
    public function percentOfCompleteTasks(): ProjectsQuery
    {
        $statusesWorks = [Tasks::STATUS_NEW, Tasks::STATUS_IN_PROGRESS, Tasks::STATUS_FEEDBACK];
        $statusFinished = [Tasks::STATUS_FINISHED, Tasks::STATUS_CLOSED];

        $this->withTasks($statusesWorks, 'all');
        $this->withTasks($statusFinished, 'finished');

        $expression = new Expression("IFNULL(COUNT(DISTINCT all.id) / COUNT(DISTINCT finished.id), 0) AS `percentComplete`");
        $this->addSelect($expression);

        return $this;
    }
}
