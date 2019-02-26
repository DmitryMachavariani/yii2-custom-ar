<?php

use yii\db\Migration;

/**
 * Class m190222_131612_create_profile
 */
class m190222_131612_create_profile extends Migration
{
    public function safeUp()
    {
        $this->createTable('profile', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'first_name' => $this->string(),
            'middle_name' => $this->string(),
            'last_name' => $this->string(),
            'photo' => $this->string(32),
            'job' => $this->string()
        ]);

        $this->createIndex('profile_index', 'profile', 'user_id');

        $this->addForeignKey('user_foreign', 'profile', 'user_id', 'users', 'id', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('user_foreign', 'profile');

        $this->dropIndex('profile_index', 'profile');

        $this->dropTable('profile');
    }
}
