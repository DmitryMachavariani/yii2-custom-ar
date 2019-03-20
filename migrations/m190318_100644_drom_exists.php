<?php

use yii\db\Migration;

/**
 * Class m190318_100644_drom_exists
 */
class m190318_100644_drom_exists extends Migration
{
    public function safeUp()
    {
        if ($this->db->getTableSchema('auth_assignment', true) !== null) {
            $this->dropTable('auth_assignment');
        }

        if ($this->db->getTableSchema('auth_rule', true) !== null) {
            $this->dropTable('auth_rule');
        }

        if ($this->db->getTableSchema('auth_item_child', true) !== null) {
            $this->dropTable('auth_item_child');
        }

        if ($this->db->getTableSchema('auth_item', true) !== null) {
            $this->dropTable('auth_item');
        }

        $this->dropColumn('users', 'status');
    }

    public function safeDown()
    {

    }
}
