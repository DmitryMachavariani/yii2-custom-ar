<?php

use yii\db\Migration;

/**
 * Class m190221_093936_add_file_external_id
 */
class m190221_093936_add_file_external_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn('files', 'model_id', $this->integer()->unsigned());
    }

    public function safeDown()
    {
        $this->dropColumn('files', 'model_id');
    }
}
