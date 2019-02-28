<?php

use yii\db\Migration;

/**
 * Class m190228_092957_insert_profile_for_default_user
 */
class m190228_092957_insert_profile_for_default_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \app\models\Profile([
            'user_id' => \app\models\Users::find()->andWhere(['username' => 'admin'])->select('id')->scalar(),
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'middle_name' => 'Admin',
            'job' => 'Admin',
        ]))->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190228_092957_insert_profile_for_default_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190228_092957_insert_profile_for_default_user cannot be reverted.\n";

        return false;
    }
    */
}
