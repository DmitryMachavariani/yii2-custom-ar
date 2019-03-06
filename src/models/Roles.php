<?php

namespace vladayson\AccessRules;

use app\models\Users;
use Yii;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $parent_id
 *
 * @property Roles $parent
 * @property Roles[] $roles
 * @property RolesPermissions[] $rolesPermissions
 * @property RolesUsers[] $rolesUsers
 */
class Roles extends BaseModel
{
    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class'     => SaveRelationsBehavior::class,
                'relations' => [
                    'permissions',
                    'roles'
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Roles::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Roles::class, ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolesPermissions()
    {
        return $this->hasMany(RolesPermissions::class, ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolesUsers()
    {
        return $this->hasMany(RolesUsers::class, ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['user_id' => 'id'])->via('rolesUsers');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(Permissions::class, ['permission_id' => 'id'])->via('rolesPermissions');
    }

    /**
     * {@inheritdoc}
     * @return RolesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RolesQuery(get_called_class());
    }

    public static function getUserAssignments($userId)
    {
        return self::find()
            ->joinWith('users')
            ->joinWith('permissions')
            ->andWhere([Users::tableName() . '.id' => $userId])
            ->select(Permissions::tableName() . '.*')
            ->all();
    }
}
