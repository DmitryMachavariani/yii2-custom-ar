<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Trackers]].
 *
 * @see Trackers
 */
class TrackersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Trackers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Trackers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
