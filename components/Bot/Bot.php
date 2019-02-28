<?php

namespace app\components\Bot;

use yii\base\Component;
use Doctrine\Common\Cache\RedisCache;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Cache\DoctrineCache;
use BotMan\Drivers\Telegram\TelegramDriver;
use \BotMan\Drivers\Telegram\TelegramPhotoDriver;
use app\components\Bot\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use yii\web\View;

/**
 * Class Bot
 *
 * @package app\components\Bot
 * @property BotMan $bot
 */
class Bot extends Component
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var bool
     */
    public $usePassword = false;

    /**
     * @var bool
     */
    public $verifySsl;

    /**
     * @var string
     */
    public $proxyUrl;

    /**
     * @var string
     */
    public $proxyAuth;

    /**
     * @var int
     */
    public $cacheTime;

    /**
     * @var BotMan
     */
    protected $bot;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $action = 'intro';

    /**
     * @var string
     */
    protected $nextAction = 'intro';

    /**
     * @var array
     */
    protected $actionParams = [];

    /**
     * @var bool|string
     */
    protected $phone = false;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var string
     */
    public $viewPath;

    /**
     * @return BotMan
     */
    public function init()
    {
        parent::init();

        $redis = new \Redis();
        $redis->connect(\Yii::$app->cache->redis->hostname, \Yii::$app->cache->redis->port);

        $cacheDriver = new RedisCache();
        $cacheDriver->setRedis($redis);
        $config = [
            'telegram' => [
                'token' => $this->token,
                'conversation_cache_time' => $this->cacheTime
            ],
            'conversation_cache_time' => $this->cacheTime,
            'config' => [
                'conversation_cache_time' => $this->cacheTime,
            ]
        ];
        DriverManager::loadDriver(TelegramDriver::class);
        DriverManager::loadDriver(TelegramPhotoDriver::class);
        $this->bot = BotManFactory::create($config, new DoctrineCache($cacheDriver));

        return $this->bot;
    }

    public function run()
    {
        try {
            $this->bot->hears('/start', function($bot) {
                /** @var $bot \BotMan\BotMan\BotMan */
                $bot->startConversation(new BotConversation($this->usePassword));
            });

            $this->bot->listen();
        } catch (\Exception $e) {
            $this->log(var_export([
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            ], 1));
            $this->bot->startConversation(new BotConversation($this->usePassword));
        }
    }

    /**
     * @param       $chatId
     * @param array $params
     *
     * @return mixed
     * @throws \BotMan\BotMan\Exceptions\Base\BotManException
     */
    public function sendCustomChat($chatId, $params = [])
    {
        $view = new View();
        $message = $view->renderFile($this->viewPath . '/' . ($params['view'] ? "{$params['view']}.php" : ''), $params);

        return $this->bot->say($message, $chatId, null, [
            'mode' => 'HTML'
        ]);
    }

    /**
     * @param        $message
     * @param string $logFile
     * @param null|string   $method
     * @param null|string   $line
     */
    public function log($message, $logFile = 'error', $method = null, $line = null)
    {
        if (!is_null($line)) {
            $message = "Line: {$line}" . PHP_EOL . $message . PHP_EOL;
        }
        if (!empty($method)) {
            $message = "Method: {$method}" . PHP_EOL . $message . PHP_EOL;
        }

        file_put_contents(
            dirname(__FILE__, 3) . "/runtime/logs/bot_{$logFile}.log",
            PHP_EOL . date('d.m.Y H:i:s') . PHP_EOL . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}