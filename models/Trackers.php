<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trackers".
 *
 * @property string $id
 * @property int $task_id
 * @property int $user_id
 * @property double $time
 * @property int $action
 * @property string $comment
 * @property string $date
 *
 * @property Tasks $task
 * @property Users $user
 */
class Trackers extends \yii\db\ActiveRecord
{
    const TYPE_ANALYZE = 0;
    const TYPE_DEVELOP = 1;
    const TYPE_SUPPORT = 2;
    const TYPE_REVIEW = 3;

    const TYPES = [
        self::TYPE_ANALYZE => 'Анализ',
        self::TYPE_DEVELOP => 'Разработка',
        self::TYPE_SUPPORT => 'Поддержка',
        self::TYPE_REVIEW => 'Проверка'
    ];

    public static function tableName()
    {
        return 'trackers';
    }

    public function rules()
    {
        return [
            [['task_id', 'action', 'time'], 'required'],
            [['task_id', 'user_id', 'action'], 'integer'],
            [['time'], 'number'],
            [['date'], 'safe'],
            [['comment'], 'string', 'max' => 255],
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
            'time' => 'Time',
            'action' => 'Action',
            'comment' => 'Comment',
            'date' => 'Date',
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
     * @return TrackersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TrackersQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return self::TYPES;
    }

    /**
     * @param int $type
     *
     * @return mixed|string
     */
    public static function getType(int $type)
    {
        return self::TYPES[$type] ?? '';
    }

    public function beforeSave($insert)
    {
        if (empty($this->date)) {
            $this->date = date('Y-m-d');
        }

        if (empty($this->user_id)) {
            $this->user_id = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }
}
