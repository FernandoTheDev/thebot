<?php
date_default_timezone_set("America/Sao_Paulo");

use Fernando\Db\ContainerDb;
use Fernando\Middleware\Middleware;
use Zanzara\{
    Zanzara,
    Config
};
use Fernando\Model\ {
    CallbackModel,
    MessageModel
};

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../environment/");
$dotenv->load();

ContainerDb::getContainer();

$config = new Config();
$config->setParseMode(Config::PARSE_MODE_MARKDOWN_LEGACY);
$config->setConnectorOptions(["dns" => "8.8.8.8"]);

$bot = new Zanzara($_ENV["TOKEN_BOT"], $config);

$bot->onMessage([MessageModel::class,"handler"])->middleware([new Middleware, "process"]);
$bot->onCbQuery([CallbackModel::class,"handler"])->middleware([new Middleware, "process"]);

$bot->run();