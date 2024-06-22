<?php

namespace Fernando\Db;

use Pimple\Container;
use PDO;

abstract class ContainerDb
{
    private static Container $container;

    public static function init(): void
    {
        self::$container = new Container();

        self::$container['pdo'] = function ($c) {
            $pdoSettings = [
                'dsn' => 'sqlite:' . __DIR__ . '/../../database/database.db',
                'username' => null,
                'password' => null,
                'options' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            ];
            return new PDO(
                $pdoSettings['dsn'],
                $pdoSettings['username'],
                $pdoSettings['password'],
                $pdoSettings['options']
            );
        };

        self::$container['test'] = function ($c) {
            return microtime(true);
        };
    }

    public static function getContainer(): Container
    {
        if (!isset(self::$container)) {
            self::init();
        }
        return self::$container;
    }
}
