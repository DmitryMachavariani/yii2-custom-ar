<?php

namespace app\components\Bot;

use app\components\Bot\Curl;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Cache\ArrayCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Drivers\NullDriver;
use BotMan\BotMan\Interfaces\CacheInterface;
use BotMan\Drivers\Telegram\TelegramDriver;
use Symfony\Component\HttpFoundation\Request;
use BotMan\BotMan\Interfaces\StorageInterface;
use BotMan\BotMan\Storages\Drivers\FileStorage;

/**
 * Class BotManFactory
 * @package app\components\Bot
 */
class BotManFactory extends \BotMan\BotMan\BotManFactory
{
    /**
     * Create a new BotMan instance.
     *
     * @param array $config
     * @param CacheInterface $cache
     * @param Request $request
     * @param StorageInterface $storageDriver
     * @return \BotMan\BotMan\BotMan
     */
    public static function create(
        array $config,
        CacheInterface $cache = null,
        Request $request = null,
        StorageInterface $storageDriver = null
    ) {
        if (empty($cache)) {
            $cache = new ArrayCache();
        }
        if (empty($request)) {
            $request = Request::createFromGlobals();
        }
        if (empty($storageDriver)) {
            $storageDriver = new FileStorage(__DIR__);
        }

        $http = new Curl();
        $driverManager = new DriverManager($config, $http);
        $driver = $driverManager->getMatchingDriver($request);
        if ($driver instanceof NullDriver) {
            $driver = new TelegramDriver($request, $config, $http);
        }

        return new BotMan($cache, $driver, $config, $storageDriver);
    }
}