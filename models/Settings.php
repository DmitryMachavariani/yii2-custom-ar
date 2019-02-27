<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $value
 *
 * @property Users $user
 */
class Settings extends \yii\db\ActiveRecord
{
    const USE_TELEGRAM = 'use_telegram';
    const TELEGRAM_ID = 'telegram_id';
    const USE_EMAIL = 'use_email';

    /**
     * @param $key
     *
     * @return mixed|string
     */
    public static function getLabel($key)
    {
        $data = [
            self::USE_TELEGRAM => 'В телеграм',
            self::TELEGRAM_ID => 'Телеграм chat ID',
            self::USE_EMAIL => 'На почту',
        ];

        return $data[$key] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'key', 'value'], 'required'],
            [['user_id'], 'integer'],
            [['key', 'value'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public function saveData()
    {
        foreach ($this->key as $i => $key) {
            $model = self::find()
                ->andWhere([
                    'user_id' => $this->user_id,
                    'key' => $key
                ])->one();
            if (!$model) {
                $model = new self();
            }
            $model->user_id = $this->user_id;
            $model->key = $key;
            $model->value = $this->value[$i];
            $model->save();
        }
    }

    /**
     * {@inheritdoc}
     * @return SettingsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SettingsQuery(get_called_class());
    }

    /**
     * @param $userId
     * @param $key
     *
     * @return false|string|null
     */
    public static function getValue($userId, $key)
    {
        return self::find()
            ->select('value')
            ->andWhere([
                'user_id' => $userId,
                'key' => $key
            ])->scalar();
    }
}
