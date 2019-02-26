<?php

use yii\db\Migration;

/**
 * Class m190219_101527_users_table
 */
class m190219_101527_users_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string(50),
            'password' => $this->string(65),
            'email' => $this->string(255),
            'status' => $this->boolean()->unsigned()
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
