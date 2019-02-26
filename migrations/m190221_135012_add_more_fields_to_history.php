<?php

use yii\db\Migration;

/**
 * Class m190221_135012_add_more_fields_to_history
 */
class m190221_135012_add_more_fields_to_history extends Migration
{
    public function safeUp()
    {
        $this->addColumn('history', 'comment', $this->text()->after('type'));
        $this->addColumn('history', 'author_id', $this->integer()->unsigned()->after('model_id'));

        $this->createIndex('author_index', 'history', 'author_id');

        $this->addForeignKey('author_foreign', 'history', 'author_id', 'users', 'id', 'cascade');
    }

    public function safeDown()
    {
        $this->dropForeignKey('author_foreign', 'history');

        $this->dropIndex('author_index', 'history');

        $this->dropColumn('history', 'comment');
        $this->dropColumn('history', 'author_id');
    }
}
