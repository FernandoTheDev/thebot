<?php

use Fernando\Core\BotCore;
use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../../vendor/autoload.php';

$connection = new AMQPStreamConnection('rabbitmq', 5673, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('shopping', false, false, false, false);

$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    
    $core = new BotCore();
    $bot = $core->getBot();

    $bot->editMessageText("Oie", $data["user_id"], $data["message_id"]);
};

$channel->basic_consume('shopping', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}