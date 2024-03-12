<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// Set up the database connection
$dsn = 'mysql:host=mysql;dbname=rabbitmq';
$username = 'akreem';
$password = 'pass';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}



$connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('my_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit, press Ctrl+C\n";

$callback = function ($msg) use ($pdo) {
    try {
        $data = json_decode($msg->body, true); // Decode the JSON data

        
        // Parse the data or use it directly
        // Example: $data = json_decode($data);

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO test (nom,prenom,elsey) VALUES (:value1, :value2, :value3)");


        $value1 = $data['value1']; // Adjust this based on your data structure
        $value2 = $data['value2']; // Adjust this based on your data structure
        $value3 = $data['value3']; // Adjust this based on your data structure


        $stmt->bindParam(':value1', $value1);
        $stmt->bindParam(':value2', $value2);
        $stmt->bindParam(':value3', $value3);

        $stmt->execute();

        echo ' [x] Inserted data: ', $msg->body, "\n";
    } catch (Exception $e) {
        echo ' [!] Error: ', $e->getMessage(), "\n";
    }
};

$channel->basic_consume('my_queue', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}
