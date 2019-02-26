<?php

use yii\db\Migration;

/**
 * Class m190219_100552_init_migration
 */
class m190219_100552_project_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('projects', [
            'id' => $this->primaryKey()->unsigned(),
            'title' => $this->string(),
            'description' => $this->text(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('projects');
    }
}
