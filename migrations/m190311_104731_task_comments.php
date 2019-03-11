<?php

use yii\db\Migration;

/**
 * Class m190311_104731_task_comments
 */
class m190311_104731_task_comments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('task_comment', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'task_id' => $this->integer(1)->unsigned()->notNull(),
            'text' => $this->text(),
            'date_updated' => $this->timestamp()->defaultExpression('NOW()'),
            'author_id' => $this->integer(1)->unsigned()->notNull(),
        ]);

        $this->addForeignKey('fk_task_comment_task', 'task_comment', 'task_id', 'tasks', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_task_comment_user', 'task_comment', 'author_id', 'users', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('task_comment');
    }
}
