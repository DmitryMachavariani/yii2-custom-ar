<?php

use yii\db\Migration;

/**
 * Class m190227_140345_tracker
 */
class m190227_140345_tracker extends Migration
{
    public function safeUp()
    {
        $this->createTable('trackers', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'time' => $this->float()->unsigned(),
            'action' => $this->boolean()->unsigned(),
            'comment' => $this->string(),
            'date' => $this->date()
        ]);

        $this->createIndex('task_index', 'trackers', 'task_id');
        $this->createIndex('user_index', 'trackers', 'user_id');

        $this->addForeignKey('foreign_task_tracker', 'trackers', 'task_id', 'tasks', 'id', 'cascade');
        $this->addForeignKey('foreign_user_tracker', 'trackers', 'user_id', 'users', 'id', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('foreign_task_tracker', 'trackers');
        $this->dropForeignKey('foreign_user_tracker', 'trackers');

        $this->dropIndex('task_index', 'trackers');
        $this->dropIndex('user_index', 'trackers');

        $this->dropTable('trackers');
    }
}
