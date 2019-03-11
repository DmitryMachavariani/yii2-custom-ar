<?php

namespace app\models;

use app\components\Helper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Class GanttForm.
 *
 * @package app\models
 */
class GanttForm extends Model
{
    const FILTER_TYPE_WORKING = 1;
    const FILTER_TYPE_ALL = 2;
    const FILTER_TYPE_DEPARTMENT = 3;
    const FILTER_TYPE_FIXTURE = 4;
    const FILTER_TYPE_CITY = 5;

    const LIMIT = 30;

    /**
     * @var Projects
     */
    public $project;

    /**
     * @var Tasks
     */
    public $tasks;

    /**
     * @var ActiveDataProvider
     */
    public $dataProvider;

    /**
     * @var array
     */
    public $paginationData;

    /**
     * @return array
     */
    public static function getFilterTypes()
    {
        return [
            self::FILTER_TYPE_CITY => 'Поселок',
            self::FILTER_TYPE_WORKING => 'Объекты в работе',
            self::FILTER_TYPE_ALL => 'Все объекты в работе',
            self::FILTER_TYPE_DEPARTMENT => 'Работы по подразделениям',
            self::FILTER_TYPE_FIXTURE => 'Объекты с отклонениями'
        ];
    }

    /**
     * @param     $params
     * @param int $page
     *
     * @return ActiveDataProvider
     */
    public function searchTasks($params, $page = 1)
    {
        $query = Tasks::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
        $model = new Tasks();
        $model->load($params);

        $query->andWhere(new Expression('NOT planned_start_date IS NULL'));
        $query->andWhere(new Expression('NOT planned_end_date IS NULL'));

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $model->id,
            'assigned_to' => $model->assigned_to,
            'date_created' => $model->date_created,
            'date_updated' => $model->date_updated,
            'project_id' => $model->date_updated,
            'planned_start_date' => $model->planned_start_date,
            'planned_end_date' => $model->planned_end_date,
            'real_start_date' => $model->real_start_date,
            'real_end_date' => $model->real_end_date,
        ]);
        $query->orderBy([
            'project_id' => SORT_ASC,
            'date_created' => SORT_ASC
        ]);

        $query->andFilterWhere(['like', 'title', $model->title])
            ->andFilterWhere(['like', 'description', $model->description]);
        $this->dataProvider = $dataProvider;

        $currentLimit = self::LIMIT;
        $currentOffset = ($page - 1) * self::LIMIT;

        $this->paginationData = Helper::getLimitAndOffset(
            $dataProvider->getTotalCount(),
            $currentOffset,
            $currentLimit
        );
        $query->limit($currentLimit);
        $query->offset($currentOffset);

        return $dataProvider;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function formatTasks()
    {
        $data = $links = [];
        $usedObjects = [];
        /** @var Tasks[] $tasks */
        $tasks = $this->dataProvider->query->all();

        foreach ($tasks as $i => $task) {
            /** @var Projects $project */
            $project = $task->project;
            if (!in_array($project->id, $usedObjects)) {
                $usedObjects[] = $project->id;
                $dates = self::calcObjectDatesFromTasks($project->id, $tasks);
                $objectData = [
                    'id' => 'ob_' . $project->id,
                    'text' => $project->title,
                    'start_date' => $dates['startDate'],
                    'duration' => $dates['duration'],
                    'progress' => 0,
                    'open' => false,
                    'type' => 'project'
                ];
                $data[] = $objectData;
            }
            $totalDuration = (new \DateTime($task->planned_end_date))->diff(new \DateTime($task->planned_start_date))->days;

            $data[] = [
                'id' => $task->id,
                'text' => "{$task->title}",
                'start_date' => $task->planned_start_date,
                'duration' => $totalDuration,
                'progress' => 0,
                'parent' => 'ob_' . $project->id,
                'open' => true,
                'type' => 'task',
                'real_start_date' => $task->real_start_date ?? false,
                'real_end_date' => $task->real_end_date ?? false,
            ];
        }

        return [$data, $links];
    }

    /**
     * @param        $projectId
     * @param Tasks[] $tasks
     *
     * @return array
     * @throws \Exception
     */
    protected static function calcObjectDatesFromTasks($projectId, $tasks)
    {
        $startDate = null;
        $endDate = null;

        foreach ($tasks as $task) {
            if ($task->project_id != $projectId) {
                continue;
            }
            if (empty($startDate) || $task->planned_start_date < $startDate) {
                $startDate = $task->planned_start_date;
            }

            if (empty($endDate) || $task->planned_end_date > $endDate) {
                $endDate = $task->planned_end_date;
            }
        }
        if (time() < strtotime($startDate)) {
            $currentDuration = 0;
        } else {
            $currentDuration = (new \DateTime())->diff(new \DateTime($startDate))->days;
        }
        $duration = (new \DateTime($endDate))->diff(new \DateTime($startDate))->days;

        return compact('startDate', 'endDate', 'duration', 'currentDuration');
    }

    /**
     * @return array
     */
    public static function getTestTask()
    {
        $data = [
            [
                'id' => 'ob_1',
                'text' => 'Object #1',
                'start_date' => '2019-01-01',
                'duration' => 20,
                'progress' => 0,
                'open' => false,
                'lag' => 2,
                'advance' => 0
            ],
            [
                'id' => 1,
                'text' => "Test Task 1",
                'start_date' => '2019-01-28',
                'duration' => 15,
                'progress' => 0,
                'parent' => 'ob_1',
                'open' => true,
                'lag' => 3,
                'lag_start' => 3,
                'lag_end' => 0,
                'advance' => 0,
                'real_start_date' => '2019-01-30',
                'real_end_date' => '2019-02-13',
                'brigade' => '',
            ],
            [
                'id' => 2,
                'text' => "Test Task 2",
                'start_date' => '2019-01-31',
                'duration' => 5,
                'progress' => 0,
                'parent' => 'ob_1',
                'open' => true,
                'lag' => 0,
                'lag_start' => 0,
                'lag_end' => 0,
                'advance' => 0,
                'real_start_date' => '2019-01-31',
                'real_end_date' => '2019-02-05',
                'brigade' => '',
            ],
            [
                'id' => 3,
                'text' => "Test Task 3",
                'start_date' => '2019-02-15',
                'duration' => 10,
                'progress' => 0,
                'parent' => 'ob_1',
                'open' => true,
                'lag' => 0,
                'lag_start' => 0,
                'lag_end' => 0,
                'advance' => 0,
                'real_start_date' => '2019-02-15',
                'real_end_date' => '2019-02-25',
                'brigade' => '',
            ],
        ];
        $links = [
            [
                'id' => '225',
                'source' => 1,
                'target' => 3,
                'type' => 0
            ],
            [
                'id' => '226',
                'source' => 1,
                'target' => 2,
                'type' => 0
            ]
        ];

        return compact('links', 'data');
    }
}
