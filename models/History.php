<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "history".
 *
 * @property int $id
 * @property int $type
 * @property string $comment
 * @property string $model_name
 * @property int $model_id
 * @property int $author_id
 * @property string $date
 *
 * @property Users $author
 */
class History extends \yii\db\ActiveRecord
{
    const TYPE_CHANGE_STATUS = 0;
    const TYPE_ADD_FILE = 1;
    const TYPE_CHANGE_ASSIGN_TO = 2;

    const TYPES = [
        self::TYPE_CHANGE_STATUS => 'Сменился статус у задачи',
        self::TYPE_CHANGE_ASSIGN_TO => 'Сменился назначенный человек',
        self::TYPE_ADD_FILE => 'Прикреплен файл',
    ];

    /**
     * Чтобы не хардкодить дописываем сюда
     */
    const MODEL_TASKS = 'Tasks';
    const MODEL_PROFILE = 'Profile';
    const MODEL_USERS = 'Users';
    const MODEL_PROJECT = 'Project';

    public static function tableName()
    {
        return 'history';
    }

    public function rules()
    {
        return [
            [['type', 'model_id', 'author_id'], 'integer'],
            [['comment'], 'string'],
            [['date'], 'safe'],
            [['model_name'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'comment' => 'Comment',
            'model_name' => 'Model Name',
            'model_id' => 'Model ID',
            'author_id' => 'Author ID',
            'date' => 'Date',
        ];
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
     * @return HistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HistoryQuery(get_called_class());
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
     * @param int $type
     *
     * @return string
     */
    public static function getIconByType (int $type): string
    {
        switch ($type) {
            case self::TYPE_CHANGE_STATUS:
                $class = 'fa-refresh';
                break;

            case self::TYPE_ADD_FILE:
                $class = 'fa-file-archive-o';
                break;

            case self::TYPE_CHANGE_ASSIGN_TO:
                $class = 'fa-user-circle-o';
                break;

            default:
                $class = '';
                break;
        }

        return $class;
    }

    /**
     * @param string $type
     * @param string $modelName
     * @param int    $modelId
     *
     * @return bool
     */
    public static function create(string $type, string $modelName, int $modelId)
    {
        $model = new History();
        $model->model_name = $modelName;
        $model->model_id = $modelId;
        $model->type = $type;
        $model->comment = History::TYPES[$type];

        return $model->save();
    }

    public function beforeSave($insert)
    {
        $this->author_id = Yii::$app->user->id;
        $this->date = date('Y-m-d G:i:s');

        return parent::beforeSave($insert);
    }
}
