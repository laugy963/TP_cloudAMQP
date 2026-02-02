<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// URL CloudAMQP
$url = "amqps://tqaoitos:JUsqKoAdXa_lqeQ2Q59I3FfueYa3UpyX@dog.lmq.cloudamqp.com/tqaoitos";
$parts = parse_url($url);

$connection = new AMQPStreamConnection(
    $parts['host'],
    5672,
    $parts['user'],
    $parts['pass'],
    ltrim($parts['path'], '/'),
    false,
    'AMQPLAIN',
    null,
    'en_US',
    3.0,
    3.0,
    null,
    false,
    5671
);

$channel = $connection->channel();

$channel->queue_declare('ma_file', false, true, false, false);

echo "En attente de messages...\n";

$callback = function ($msg) {
    echo "Message reçu : " . $msg->body . "\n";
};

$channel->basic_consume('ma_file', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
