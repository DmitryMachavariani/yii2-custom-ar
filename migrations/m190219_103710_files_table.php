<?php

use yii\db\Migration;

/**
 * Class m190219_103710_files_table
 */
class m190219_103710_files_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey()->unsigned(),
            'model_name' => $this->string(),
            'name' => $this->string(),
            'status' => $this->boolean()->unsigned(),
            'date_created' => $this->dateTime(),
            'date_updated' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('files');
    }
}
