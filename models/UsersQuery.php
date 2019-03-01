<?php

namespace app\models;

use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Users]].
 *
 * @see Users
 */
class UsersQuery extends \yii\db\ActiveQuery
{
    public function withProfile()
    {
        return $this->joinWith(['profile p']);
    }

    /**
     * {@inheritdoc}
     * @return Users[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    public function fioAndPositionAsUsername()
    {
        return $this->withProfile()
            ->select([Users::tableName() . '.id', new Expression("CONCAT (p.last_name, p.first_name, '(', p.job, ')') AS username")]);
    }

    /**
     * {@inheritdoc}
     * @return Users|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
