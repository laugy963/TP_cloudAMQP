<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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
    5671 // port SSL
);

$channel = $connection->channel();

// Déclarer la file
$channel->queue_declare('ma_file', false, true, false, false);

$messageBody = "Bonjour depuis le programme PHP 1 👋";
$msg = new AMQPMessage($messageBody);

$channel->basic_publish($msg, '', 'ma_file');

echo "Message envoyé : $messageBody\n";

$channel->close();
$connection->close();
