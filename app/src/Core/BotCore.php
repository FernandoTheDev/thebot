<?php
namespace Fernando\Core;

use PDO;
use Dotenv\Dotenv;
use Fernando\Db\ContainerDb;
use SergiX44\Nutgram\Nutgram;

final class BotCore
{
    private Nutgram $bot;
    private PDO $pdo;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../environment/");
        $dotenv->load();

        $this->pdo = ContainerDb::getContainer()["pdo"];
        $this->bot = new Nutgram($_ENV["TOKEN_BOT"]);
    }

    public function getBot() : Nutgram
    {
        return $this->bot;
    }

    public function getPdo() : PDO
    {
        return $this->pdo;
    }
}