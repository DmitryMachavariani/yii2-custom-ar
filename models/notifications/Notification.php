<?php

namespace app\models\notifications;

use app\models\Tasks;
use app\models\Users;
use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property string $id
 * @property int $task_id
 * @property int $user_id
 * @property int $type
 * @property int $status
 * @property string $date_created
 * @property string $date_updated
 * @property string $description
 *
 * @property Tasks $task
 * @property Users $user
 */
class Notification extends \yii\db\ActiveRecord
{
    /** ТИПЫ УВЕДОМЛЕНИЙ */
    const TYPE_INSIDE = 0;
    const TYPE_MAIL = 1;
    const TYPE_TELEGRAM = 2;

    const TYPES = [
        self::TYPE_INSIDE => 'Внутренние уведомление',
        self::TYPE_MAIL => 'Уведомление на почту',
        self::TYPE_TELEGRAM => 'Уведомление в Telegram'
    ];

    /** СТАТУСЫ УВЕДОМЛЕНИЙ */
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;

    const STATUSES = [
        self::STATUS_NEW => 'Новая',
        self::STATUS_IN_PROGRESS => 'В работе',
        self::STATUS_FINISHED => 'Завершена',
    ];

    public static function tableName()
    {
        return 'notifications';
    }

    public function rules()
    {
        return [
            [['task_id', 'user_id', 'type', 'status'], 'integer'],
            [['date_created', 'date_updated'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'status' => 'Status',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return NotificationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return self::TYPES;
    }

    /**
     * @param int $type
     *
     * @return mixed|string
     */
    public static function getType(int $type): string
    {
        $types = self::getTypes();

        return $types[$type] ?? '';
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    /**
     * @param int $status
     *
     * @return mixed|string
     */
    public static function getStatus(int $status): string
    {
        $statuses = self::getStatuses();

        return $statuses[$status] ?? '';
    }

    /**
     * @param int|null $notifyId
     *
     * @return int
     */
    public static function setNotifyRead(int $notifyId = null): int
    {
        $userId = Yii::$app->user->id;
        $condition = ['user_id' => $userId, 'status' => Notification::STATUS_IN_PROGRESS];
        if ($notifyId)
        {
            $condition[] = ['id' => $notifyId];
        }

        return self::updateAll(['status' => Notification::STATUS_FINISHED], $condition);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->date_created = date('Y-m-d G:i:s');
        }

        $this->date_updated = date('Y-m-d G:i:s');

        return parent::beforeSave($insert);
    }
}
