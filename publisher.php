<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('my_queue', false, true, false, false);

$data = [
    'value1' => 'John Doe',
    'value2' => 'john@example.com',
    'value3' => 'example.com'
];
$messageBody = json_encode($data);
$message = new AMQPMessage($messageBody);

$channel->basic_publish($message, '', 'my_queue');

$channel->close();
$connection->close();
