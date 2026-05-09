<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

class RedisClient
{
    private static ?Client $instance = null;

    public static function connect(): ?Client
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            self::$instance = new Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);

            // проверка соединения
            self::$instance->connect();

            return self::$instance;
        } catch (Exception $e) {
            // Redis не должен ломать приложение
            return null;
        }
    }
}