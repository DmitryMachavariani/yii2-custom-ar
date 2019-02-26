<?php

namespace app\models\notifications;

/**
 * This is the ActiveQuery class for [[Notification]].
 *
 * @see Notification
 */
class NotificationQuery extends \yii\db\ActiveQuery
{
    public function my()
    {
        $userId = \Yii::$app->user->id;

        return $this->andWhere(['user_id' => $userId]);
    }

    public function inside()
    {
        return $this->andWhere(['type' => Notification::TYPE_INSIDE]);
    }

    public function withStatus(int $status)
    {
        return $this->andWhere(['status' => $status]);
    }

    /**
     * {@inheritdoc}
     * @return Notification[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Notification|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
