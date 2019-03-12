<?php

namespace app\models;

use app\components\notification\NotifyFactory;
use yii\base\Model;

/**
 * Class SendMessageForm.
 *
 * @package app\models
 */
class SendMessageForm extends Model
{
    public $userIds;
    public $message;

    public function rules()
    {
        return [
            [['userIds', 'message'], 'required'],
            [['message'], 'string', 'max' => 255],
            [['userIds'], 'each', 'rule' => ['integer']],
        ];
    }

    public function send()
    {
        if (!$this->validate()) {
            return false;
        }

        NotifyFactory::notifyUser($this->userIds, null, ['view' => 'message', 'message' => $this->message]);

        return true;
    }
}
