<?php

use yii\db\Migration;

/**
 * Class m190227_150853_add_user_phone
 */
class m190227_150853_add_user_phone extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'phone', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('users', 'phone');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190227_150853_add_user_phone cannot be reverted.\n";

        return false;
    }
    */
}
