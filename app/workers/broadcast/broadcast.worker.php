<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once __DIR__ . '/../../vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('broadcast', false, false, false, false);

echo "Aguardando solicitações...\n";

$callback = function ($msg) {
    $data = json_decode($msg->body, true);
    $message = substr($data["message"], 1, strlen($data["message"]));
};

$channel->basic_consume('broadcast', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}