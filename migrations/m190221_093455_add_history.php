<?php

use yii\db\Migration;

/**
 * Class m190221_093455_add_history
 */
class m190221_093455_add_history extends Migration
{
    public function safeUp()
    {
        $this->createTable('history', [
            'id' => $this->bigPrimaryKey(20)->unsigned(),
            'type' => $this->boolean(),
            'model_name' => $this->string(),
            'model_id' => $this->integer()->unsigned(),
            'date' => $this->dateTime(),
        ]);

        $this->createIndex('model_name', 'history', 'model_name');
        $this->createIndex('model_id', 'history', 'model_id');
    }

    public function safeDown()
    {
        $this->dropIndex('model_name', 'history');
        $this->dropIndex('model_id', 'history');

        $this->dropTable('history');
    }
}
