<?php

use yii\db\Migration;

/**
 * Class m190221_095635_add_created_by_to_tasks
 */
class m190221_095635_add_created_by_to_tasks extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tasks', 'created_by', $this->integer()->unsigned()->after('assigned_to'));

        $this->createIndex('created_by_index', 'tasks', 'created_by');

        $this->addForeignKey('created_by_foreign', 'tasks', 'created_by', 'users', 'id', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('created_by_foreign', 'tasks');

        $this->dropIndex('created_by_index', 'tasks');

        $this->dropColumn('tasks', 'created_by');
    }
}
