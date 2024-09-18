<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;
use React\EventLoop\Factory as EventLoopFactory;

class WebSocketServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Broadcast the message to all clients
        foreach ($this->clients as $client) {
            if ($from == $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    // Method to broadcast a message from SHAD API to all clients
    public function broadcastMessage($message) {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    // Method to check for new messages in the file and broadcast them
    public function checkForShadMessages() {
        $file = 'shad_messages.txt';

        if (file_exists($file)) {
            // Read all messages from the file
            $messages = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Send each message to all connected clients
            foreach ($messages as $message) {
                $this->broadcastMessage($message);
            }

            // Clear the file after sending messages
            file_put_contents($file, '');
        }
    }
}

// Create the ReactPHP event loop
$loop = EventLoopFactory::create();

// Create the WebSocket server
$server = new WebSocketServer();

// Start WebSocket server using Ratchet and ReactPHP event loop
$app = new App('localhost', 8080, '0.0.0.0', $loop);
$app->route('/chat', $server, ['*']);

// Periodically check for SHAD API messages every second
$loop->addPeriodicTimer(1, function() use ($server) {
    $server->checkForShadMessages();
});

// Run the event loop
$app->run();
