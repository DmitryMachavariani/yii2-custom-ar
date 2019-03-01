<?php

namespace app\models;

use app\components\Helper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property int $status
 *
 * @property Profile $profile
 * @property Settings[] $settings
 * @property Projects[] $projects
 */
class Users extends User
{
    public $user_id;
    public $settingsModel;
    public $repeatPassword;

    /** STATUSES */
    const STATUS_USER = 1;
    const STATUS_ADMIN = 2;
    const STATUS_BANNED = 3;

    const STATUSES = [
        self::STATUS_USER => 'Пользователь',
        self::STATUS_ADMIN => 'Администратор',
        self::STATUS_BANNED => 'Заблокированный'
    ];

    const SCENARIO_UPDATE = 'update_users';

    public function scenarios()
    {
        return [
            self::SCENARIO_UPDATE => [],
        ];
    }

    public function rules()
    {
        return [
            [['username', 'password', 'email', 'status'], 'required'],
            ['password', 'safe'],
            ['email', 'email'],
            [['status'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 65],
            [['email'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 255],

//            [['profile'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'repeatPassword' => 'Повторите пароль',
            'email' => 'Email',
            'phone' => 'Телефон',
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
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasMany(Settings::class, ['user_id' => 'id']);
    }

    /**
     * @param bool $insert
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        try {
            $this->phone = Helper::minimizePhone($this->phone);
        } catch (\Exception $e) {

        }

        if ($this->scenario == self::SCENARIO_UPDATE) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        try {
            $this->phone = Helper::maximizePhone($this->phone);
        } catch (\Exception $e) {

        }

        return parent::afterFind();
    }

    /**
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public function saveSetting($key, $value)
    {
        $setting = Settings::find()
            ->andWhere([
                'user_id' => $this->id,
                'key' => $key
            ])->one();

        if (!$setting) {
            $setting = new Settings([
                'user_id' => $this->id,
                'key' => $key,
            ]);
        }
        $setting->value = (string)$value;

        if (!$setting->save()) {
            \Yii::$app->bot->log(var_export([
                $setting->getErrors(),
                $setting->getAttributes()
            ], 1), 'test-errror');
        }

        return $setting->save();
    }

    /**
     * @return bool
     */
    public function hasTelegram()
    {
        return Settings::getValue($this->id, Settings::USE_TELEGRAM) && Settings::getValue($this->id, Settings::TELEGRAM_ID);
    }

    /**
     * @return bool
     */
    public function hasEmail()
    {
        return Settings::getValue($this->id, Settings::USE_EMAIL) && !empty($this->email);
    }

    /**
     * @return false|string|null
     */
    public function getTelegramId()
    {
        return Settings::getValue($this->id, Settings::TELEGRAM_ID);
    }

    /**
     * @return array
     */
    public function getNotifications()
    {
        $settings = Settings::find()
            ->select(['key', 'value'])
            ->andWhere([
                'user_id' => $this->id,
                'key' => [
                    Settings::USE_EMAIL,
                    Settings::USE_TELEGRAM,
                ]
            ])->all();

        return ArrayHelper::map($settings, 'key', 'value');
    }

    /**
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
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
     * @return string
     */
    public static function getStatus (int $status): string
    {
        return self::STATUSES[$status] ?? '';
    }

    /**
     * @param $phone
     *
     * @return Users|array|null
     */
    public static function getByPhone($phone)
    {
        return self::find()
            ->andWhere(['phone' => Helper::minimizePhone($phone)])
            ->one();
    }

    /**
     * @param $telegramId
     *
     * @return Users|array|null
     */
    public static function getByChatId($telegramId)
    {
        return self::find()
            ->joinWith('settings')
            ->andWhere([
                Settings::tableName() . '.key' => Settings::TELEGRAM_ID,
                Settings::tableName() . '.value' => $telegramId,
            ])->one();
    }

    public function generatePassword()
    {
        $password = Yii::$app->security->generateRandomString(rand(8, 12));
        $this->password = Yii::$app->security->generatePasswordHash($password);

        return $password;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUser()
    {
        return $this->hasMany(ProjectUser::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Projects::class, ['id' => 'project_id'])->via('projectUser');
    }
}
