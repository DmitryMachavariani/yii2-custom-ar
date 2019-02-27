<?php

namespace app\components\Bot;

use app\models\Settings;
use app\models\Users;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Attachments\File;
use yii\base\Security;
use app\components\Helper;
use app\models\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

use yii\helpers\ArrayHelper;

/**
 * Class BotConversation.
 *
 * @package app\components\Bot
 *
 * https://botman.io/2.0/conversations
 */
class BotConversation extends Conversation
{
    /**
     * @var string
     */
    protected $searchQuery;

    /**
     * @var array
     */
    protected $buttons;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var int
     */
    protected $chatId;

    /**
     * @var User
     */
    protected $systemUser;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @var bool
     */
    private $usePassword;

    /**
     * BotConversation constructor.
     *
     * @param bool $usePassword
     */
    public function __construct($usePassword = true)
    {
        $this->usePassword = $usePassword;
    }

    /**
     * @param null|string $text
     * @param null        $logData
     *
     * @return BotConversation
     */
    protected function onError($text = null, $logData = null)
    {
        $this->error = false;
        \Yii::$app->bot->log($text . PHP_EOL . var_export($logData, 1), 'error');

        return $this->intro($text);
    }

    /**
     * @return Button
     */
    private function getStartButton()
    {
        return Button::create('Старт')->value('/start');
    }

    /**
     * @param bool $checkUser
     *
     * @return bool
     * @throws \Exception
     */
    protected function beforeAction($checkUser = true)
    {
        if ($checkUser && empty($this->systemUser)) {
            throw new \Exception('Вы не авторизованы');
        }
        if ($this->getBot()->getMessage()->getText() == '/start') {
            throw new \Exception('В начало');
        }

        return true;
    }

    /**
     * @param null|string $text
     *
     * @return $this
     */
    protected function intro($text = null)
    {
        if (empty($text)) {
            $text = 'Добро пожаловать в GNS!' . PHP_EOL .
                    'Я - bot, который призван оповещать вас обо всем';
        }
        $text .= '.' . PHP_EOL . 'Для продолжения нажмите Далее';
        $question = Question::create($text)
            ->addButtons([
                Button::create('Далее')->value('yes'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->askPhone();
            }
        });
    }

    /**
     * @return $this|BotConversation
     * @throws \Exception
     */
    protected function askPhone()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_KEYBOARD)
            ->addRow(KeyboardButton::create('Разрешить контакт')->requestContact())
            ->addRow(KeyboardButton::create('Домой')->url('/start'))
            ->oneTimeKeyboard(true)
            ->resizeKeyboard(true);

        if (!$this->systemUser) {
            return $this->ask(
                'Для продолжения нажмите кнопку Разрешить',
                function (Answer $answer) {
                    $payload = (array)$answer->getMessage()->getPayload();
                    $payload = reset($payload);
                    if (!$payload['contact']['phone_number']) {
                        return $this->intro('Вы должны разрешить передачу своего контакта');
                    }
                    $this->phone = '+' . preg_replace('/[\s\+]/', '', $payload['contact']['phone_number']);
                    /** @var Users $user */
                    $user = Users::getByPhone($this->phone);
                    if (!$user) {
                        return $this->intro('Такой пользователь не зарегистрирован в системе');
                    }
                    $is = $user->saveSetting(Settings::TELEGRAM_ID, $this->chatId);

                    $this->systemUser = $user;
                    return $this->askPassword();
                },
                $keyboard->toArray()
            );
        } else {
            $this->phone = $this->systemUser->username;
            return $this->askPassword();
        }
    }

    /**
     * @return $this|BotConversation
     * @throws \Exception
     */
    protected function askPassword()
    {
        if (!$this->usePassword) {
            return $this->thanksPage();
        }
        return $this->ask(
            'Ваш номер телефона: ' . $this->phone . '. Введите пароль',
            function (Answer $answer) {
                $this->password = $answer->getText();
                $security = new Security();
                if (
                    $this->systemUser &&
                    $this->password &&
                    $security->validatePassword($this->password, $this->systemUser->password_hash)
                ) {
                    return $this->thanksPage();
                }

                return $this->onError('Неверный пароль');
            }
        );
    }

    public function thanksPage()
    {
        $this->say('Принял. Ждите уведомлений');
    }

    /**
     * @return BotConversation|mixed
     */
    public function run()
    {
        try {
            if ($this->bot->getUser()) {
                $this->chatId = $this->bot->getUser()->getId();
                $this->systemUser = Users::getByChatId($this->chatId);
            }
            return $this->intro();
        } catch (\Exception $e) {
            return $this->onError('Возникла ошибка', $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    /**
     * @param string|\BotMan\BotMan\Messages\Outgoing\Question $message
     * @param array $additionalParameters
     * @return $this
     */
    public function say($message, $additionalParameters = [])
    {
        $this->data['payloadData'] = $this->bot->reply($message, $additionalParameters);

        return $this;
    }
}
