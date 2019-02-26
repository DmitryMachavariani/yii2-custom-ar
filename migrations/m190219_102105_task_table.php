<?php

use yii\db\Migration;

/**
 * Class m190219_102105_task_table
 */
class m190219_102105_task_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tasks', [
            'id' => $this->primaryKey()->unsigned(),
            'project_id' => $this->integer()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'status' => $this->boolean()->unsigned(),
            'priority' => $this->boolean()->unsigned(),
            'assigned_to' => $this->integer()->unsigned(),
            'estimate' => $this->float(),
            'notify' => $this->integer()->unsigned(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);

        $this->createIndex('project_index', 'tasks', 'project_id');

        $this->addForeignKey('project_foreign', 'tasks', 'project_id', 'projects', 'id', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('project_foreign', 'tasks');

        $this->dropTable('tasks');
    }
}
