<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class User
 *
 * @package app\models
 *
 * @property $profile Profile
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{

//    public $id;
//    public $username;
//    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];

    public static function tableName()
    {
        return 'users';
    }

    public static function findIdentity($id)
    {
        $user = Users::findOne($id);
        return new static($user);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = Users::find()
                ->alias('u')
                ->joinWith('profile p')
                ->where(['u.username' => $username])
                ->one();

        return new static($user);
    }

    /**
     * @return User|array|ActiveRecord|null
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id'])->one();
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }
}
