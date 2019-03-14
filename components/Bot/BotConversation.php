<?php

namespace app\components\Bot;

use app\models\Files;
use app\models\Settings;
use app\models\Tasks;
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
     * @var string
     */
    private $startAction = 'intro';

    /**
     * @var array
     */
    protected $inputParams = [];

    /**
     * BotConversation constructor.
     *
     * @param bool   $usePassword
     * @param string $action
     * @param array  $params
     */
    public function __construct($usePassword = true, $action = 'intro', $params = [])
    {
        $this->usePassword = $usePassword;
        $this->startAction = $action;
        $this->inputParams = $params;
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
        return $this->say('Принял. Ждите уведомлений');
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
            return $this->{$this->startAction}();
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

    /**
     * @return BotConversation|bool
     */
    protected function getTask()
    {
        /** @var Tasks $task */
        $task = Tasks::findOne($this->inputParams['taskId'] ?? 0);
        if (!$task) {
            return $this->say('Задача не найдена');
        }
        $baseUrl = \Yii::$app->params['baseUrl'];
        $text = "<b>Задача:</b> <a href=\"{$baseUrl}tasks/task?taskId={$task->id}\">{$task->title}</a>" . PHP_EOL . PHP_EOL;
        $text .= \Yii::$app->bot->stripTags($task->description) . PHP_EOL;
        $text .= "<b>Назначена на:</b> {$task->assigned->username}" . PHP_EOL;
        $text .= "<b>Сроки сдачи:</b> {$task->planned_start_date} - {$task->planned_end_date}" . PHP_EOL;
        $text .= "<b>Статус:</b> {$task->getStatusDescription()}" . PHP_EOL;
        $text .= "<b>Приоритет:</b> {$task->getPriorityDescription()}" . PHP_EOL;

        if ($task->comments) {
            $text .= PHP_EOL . "<b>Комменты:</b>" . PHP_EOL;
            $text .= '-----------' . PHP_EOL;
            foreach ($task->comments as $comment) {
                $text .= "{$comment->date_updated} <b>{$comment->author->username}</b> написал(-а):" . PHP_EOL;
                $text .= '<pre>' . strip_tags($comment->text) . '</pre>' . PHP_EOL;
                $text .= '-----------' . PHP_EOL;
            }
        }

        if ($task->attachments) {
            $text .= PHP_EOL . "<b>Файлы:</b>" . PHP_EOL;
            foreach ($task->attachments as $file) {
                $text .= '    * <a href="'.$baseUrl . 'tasks/view-file?file_id='.$file->id.'">'.$file->name.'</a>' . PHP_EOL;
            }
        }
        $messages = Helper::splitMessage($text, \Yii::$app->bot->maxMessageSymbols);
        if (count($messages) > 1) {
            for ($i = 0; $i < count($messages) - 1; $i++) {
                $this->say($messages[$i], ['parse_mode'=>'HTML']);
            }
            $messageText = $messages[count($messages) - 1];
        } else {
            $messageText = $messages[0] ?? 'Не найдено';
        }
        $question = Question::create($messageText)->addButtons([
            Button::create('Добавить коммент')->value('add_comment')
        ]);

        return $this->ask(
            $question,
            function (Answer $answer) {
                $this->beforeAction();
                $value = $answer->getText();
                if ($answer->isInteractiveMessageReply()) {
                    switch ($value) {
                        case 'add_comment':
                            return $this->addComment();
                            break;
                        default:
                            break;
                        }
                }

                return true;
            },
            ['parse_mode'=>'HTML']
        );
    }

    /**
     * @return BotConversation
     */
    public function addComment()
    {
        /** @var Tasks $task */
        $task = Tasks::findOne($this->inputParams['taskId'] ?? 0);
        if (!$task) {
            return $this->say('Задача не найдена');
        }
        $question = Question::create("Введите комментарий")->addButtons([
            Button::create('Назад')->value('back'),
        ]);

        return $this->ask($question, function (Answer $answer) use ($task) {
            $this->beforeAction();
            if (!$answer->isInteractiveMessageReply()) {
                $commentText = $answer->getText();
                $message = $task->addComment($commentText, $this->systemUser->id) ? 'Добавлено' : 'Неудача';
                $this->say($message);
            }

            return $this->getTask();
        });
    }

    /**
     * @deprecated
     * @return BotConversation
     */
    protected function getFile()
    {
        $file = Files::findOne($this->inputParams['fileId'] ?? 0);
        if (!$file) {
            return $this->say('Файл не найден');
        }

        $attachment = new File(
            \Yii::$app->params['baseUrl'] . 'uploads/test.pdf?v=' . rand(8324, 324543653),
            ['custom_payload' => true,]
        );
        $message = OutgoingMessage::create('')
            ->withAttachment($attachment);

        return $this->say($message);
    }
}
