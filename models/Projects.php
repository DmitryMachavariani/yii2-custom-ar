<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $date_created
 * @property string $date_updated
 *
 *
 * @property int $percentComplete
 *
 * @property Tasks[] $tasks
 * @property Users[] $members
 */
class Projects extends \yii\db\ActiveRecord
{
    public $percentComplete;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            [['description'], 'string'],
            [['date_created', 'date_updated'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class'     => SaveRelationsBehavior::class,
                'relations' => [
                    'members',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'description' => 'Описание',
            'date_created' => 'Дата создания',
            'date_updated' => 'Дата обновления',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(): ActiveQuery
    {
        return $this->hasMany(Tasks::class, ['project_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ProjectsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProjectsQuery(get_called_class());
    }

    public function getProjectUser()
    {
        return $this->hasMany(ProjectUser::class, ['project_id' => 'id']);
    }

    public function getMembers()
    {
        return $this->hasMany(Users::class, ['id' => 'user_id'])->via('projectUser');
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->date_created = date('Y-m-d G:i:s');
        }

        $this->date_updated = date('Y-m-d G:i:s');

        return parent::beforeSave($insert);
    }

    /**
     * @param $usersIds
     *
     * @return bool
     */
    public function addMembers($usersIds)
    {
        $this->members = $usersIds;
        return $this->save();
    }
}
