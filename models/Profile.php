<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $photo
 * @property string $job
 *
 * @property string $fullName
 *
 * @property Users $user
 */
class Profile extends \yii\db\ActiveRecord
{
    public $fullName;

    public static function tableName()
    {
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'job'], 'string', 'max' => 255],

            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['photo'], 'file', 'skipOnEmpty' => true, 'skipOnError' => true, 'extensions' => ['png', 'jpg', 'jpeg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'last_name' => 'Фамилия',
            'photo' => 'Фото',
            'job' => 'Должность',
            'fullName' => 'ФИО'
        ];
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
     * @return ProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfileQuery(get_called_class());
    }

    public function afterFind()
    {
        $this->fullName = $this->first_name . ' ' . $this->last_name;

        parent::afterFind();
    }

    public function getAvatar($width = 160, $height = 160)
    {
        if ($this->photo) {
            return '/thumbs/' . Yii::$app->thumbnail->url(
                    $this->photo,
                    [
                        'thumbnail' => [
                            'width' => $width,
                            'height' => $height,
                        ]
                    ]
                );
        }

        return '/img/default-avatar.png';
    }
}
