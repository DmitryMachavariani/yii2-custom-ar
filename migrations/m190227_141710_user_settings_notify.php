<?php

use yii\db\Migration;

/**
 * Class m190227_141710_user_settings_notify
 */
class m190227_141710_user_settings_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer(10)->unsigned()->notNull(),
            'key' => $this->string(255)->notNull(),
            'value' => $this->string(255)->notNull(),
        ]);
        $this->addForeignKey('fk_user_settings', 'settings', 'user_id', 'users', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('settings');
    }
}
