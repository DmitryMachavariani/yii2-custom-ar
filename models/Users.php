<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property int $status
 *
 * @property Profile $profile
 */
class Users extends User
{
    /** STATUSES */
    const STATUS_USER = 1;
    const STATUS_ADMIN = 2;
    const STATUS_BANNED = 3;

    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 65],
            [['email'], 'string', 'max' => 255],

            [['profile'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'email' => 'Email',
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    /**
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }
}
