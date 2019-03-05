<?php

use yii\db\Migration;

/**
 * Class m190305_100953_add_new_roles
 */
class m190305_100953_add_new_roles extends Migration
{
    /**
     * @var string the name of the table storing authorization items. Defaults to "auth_item".
     */
    public $itemTable = '{{%auth_item}}';
    /**
     * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
     */
    public $itemChildTable = '{{%auth_item_child}}';
    /**
     * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
     */
    public $assignmentTable = '{{%auth_assignment}}';
    /**
     * @var string the name of the table storing rules. Defaults to "auth_rule".
     */
    public $ruleTable = '{{%auth_rule}}';

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[parent]]) REFERENCES ' . $this->itemTable . ' ([[name]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            'FOREIGN KEY ([[child]]) REFERENCES ' . $this->itemTable . ' ([[name]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
        ], $tableOptions);

        $this->createTable($this->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->addColumn($this->itemTable, 'rule_name', $this->string(64));
        $this->addColumn($this->itemTable, 'data', $this->binary());
        $this->addColumn($this->itemTable, 'created_at', $this->integer());
        $this->addColumn($this->itemTable, 'updated_at', $this->integer());

        $this->addColumn($this->assignmentTable, 'created_at', $this->integer());

        $item = new \app\models\AuthItem();
        $item->name = 'pm';
        $item->type = 1;
        $item->description = 'Руководитель проекта';
        $item->save();

        $item = new \app\models\AuthItem();
        $item->name = 'sg';
        $item->type = 1;
        $item->description = 'Супер гость';
        $item->save();

        $canEditRealDates = $auth->createPermission('editRealDates');
        $canEditRealDates->description = 'Возможность редактировании реальных дат';

        $canCloseTask = $auth->createPermission('closeTasks');
        $canCloseTask->description = 'Возможность закрывать задачи';

        $auth->add($canEditRealDates);
        $auth->add($canCloseTask);

        $roleAdmin = $auth->getRole('admin');

        $auth->addChild($roleAdmin, $canEditRealDates);
        $auth->addChild($roleAdmin, $canCloseTask);
    }

    public function safeDown()
    {
        \app\models\AuthItem::deleteAll(['name' => 'pm']);
        \app\models\AuthItem::deleteAll(['name' => 'sg']);
    }

    protected function buildFkClause($delete = '', $update = '')
    {
        return implode(' ', ['', $delete, $update]);
    }
}
