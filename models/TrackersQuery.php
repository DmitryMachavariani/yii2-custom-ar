<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Trackers]].
 *
 * @see Trackers
 */
class TrackersQuery extends \yii\db\ActiveQuery
{
    public function withUser()
    {
        return $this->joinWith(['user']);
    }

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
