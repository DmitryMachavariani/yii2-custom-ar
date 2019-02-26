<?php

use yii\db\Migration;

/**
 * Class m190226_073229_notification
 */
class m190226_073229_notification extends Migration
{
    public function safeUp()
    {
        $defaultStatus = 0; // 0 - это статус НОВАЯ, т.к модель ещё не сгенерирована, то забиваем хардкодом

        $this->createTable('notifications', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'task_id' => $this->integer()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'type' => $this->boolean()->unsigned()->defaultValue($defaultStatus),
            'status' => $this->boolean()->unsigned(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime()
        ]);

        $this->createIndex('task_index', 'notifications', 'task_id');
        $this->createIndex('user_index', 'notifications', 'user_id');

        $this->addForeignKey('foreign_task', 'notifications', 'task_id', 'tasks', 'id', 'cascade');
        $this->addForeignKey('foreign_user', 'notifications', 'user_id', 'users', 'id', 'cascade');
    }

    public function safeDown()
    {
        $this->dropIndex('task_index', 'notifications');
        $this->dropIndex('user_index', 'notifications');

        $this->dropForeignKey('foreign_user', 'notifications');
        $this->dropForeignKey('foreign_task', 'notifications');

        $this->dropTable('notifications');
    }
}
