<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_comment".
 *
 * @property string $id
 * @property int $task_id
 * @property string $text
 * @property string $date_updated
 * @property int $author_id
 *
 * @property Tasks $task
 * @property Users $author
 */
class TaskComment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'author_id', 'text'], 'required'],
            [['task_id', 'author_id'], 'integer'],
            [['text'], 'string'],
            [['date_updated'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'text' => 'Text',
            'date_updated' => 'Date Updated',
            'author_id' => 'Author ID',
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
    public function getAuthor()
    {
        return $this->hasOne(Users::class, ['id' => 'author_id']);
    }

    /**
     * {@inheritdoc}
     * @return TaskCommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskCommentQuery(get_called_class());
    }
}
