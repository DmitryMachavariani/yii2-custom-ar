<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TaskComment]].
 *
 * @see TaskComment
 */
class TaskCommentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TaskComment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TaskComment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
