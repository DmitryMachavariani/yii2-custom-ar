<?php

use yii\db\Migration;

/**
 * Class m190228_080605_tasks_dates
 */
class m190228_080605_tasks_dates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $task = \app\models\Tasks::tableName();

        $this->addColumn($task, 'planned_start_date', $this->date());
        $this->addColumn($task, 'planned_end_date', $this->date());
        $this->addColumn($task, 'real_start_date', $this->date());
        $this->addColumn($task, 'real_end_date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $task = \app\models\Tasks::tableName();

        $this->dropColumn($task, 'planned_start_date');
        $this->dropColumn($task, 'planned_end_date');
        $this->dropColumn($task, 'real_start_date');
        $this->dropColumn($task, 'real_end_date');
    }
}
