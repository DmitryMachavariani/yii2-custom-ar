<?php

namespace app\models;

use app\components\FileHelper;
use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $model_name
 * @property string $name
 * @property int $status
 * @property string $date_created
 * @property string $date_updated
 * @property int $model_id
 * @property Tasks $task
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'model_id'], 'integer'],
            [['date_created', 'date_updated'], 'safe'],
            [['model_name', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_name' => 'Model Name',
            'name' => 'Name',
            'status' => 'Status',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
        ];
    }

    /**
     * {@inheritdoc}
     * @return FilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FilesQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'model_id']);
    }

    /**
     * @return Files
     */
    public static function createForTask()
    {
        $file = new self();
        $file->model_name = 'Task';

        return $file;
    }

    /**
     * @return string
     */
    public function getRelativeFilePath()
    {
        return mb_strtolower($this->model_name . '/' . $this->model_id . '/');
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return Yii::$app->params['baseUrl'] . '/uploads/' . $this->getRelativeFilePath() . $this->name . '?v=' . rand(1, 99999);
    }

    /**
     * @return string
     */
    public function getInternalFileUrl()
    {
        return '/tasks/download?file_id=' . $this->id;
    }

    public function afterDelete()
    {
        if ($this->name) {
            $file = new FileHelper(\Yii::getAlias('@uploads') . '/' . $this->getRelativeFilePath());
            $file->delete();
        }

        return parent::afterDelete();
    }

    /**
     * @return mixed|string|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getMimeType()
    {
        $fHelper = new FileHelper($this->name, Yii::getAlias('@uploads') . '/' . $this->getRelativeFilePath());

        return $fHelper->getMime();
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return Yii::getAlias('@uploads') . '/' . $this->getRelativeFilePath() . $this->name;
    }
}
