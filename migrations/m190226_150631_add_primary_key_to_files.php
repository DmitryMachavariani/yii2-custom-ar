<?php

use yii\db\Migration;

/**
 * Class m190226_150631_add_primary_key_to_files
 */
class m190226_150631_add_primary_key_to_files extends Migration
{
    public function safeUp()
    {
        $this->addPrimaryKey('pk', 'files', 'id');
    }

    public function safeDown()
    {
        $this->dropPrimaryKey('pk', 'files');
    }
}
