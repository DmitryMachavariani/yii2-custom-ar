<?php

use yii\db\Migration;

/**
 * Class m190226_082823_notification_text
 */
class m190226_082823_notification_text extends Migration
{
    public function safeUp()
    {
        $this->addColumn('notifications', 'description', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('notifications', 'description');
    }
}
