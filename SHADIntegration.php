<?php
// WebSocketServer.php

require 'vendor/autoload.php'; // Adjust this based on your setup

use ShadPHP\ShadPHP;

$account = new ShadPHP('989*********'); // Only without zero and with area code 98
// SHADIntegration.php

// Function to simulate receiving a message from the SHAD API
$account->onUpdate(function (array $update) use ($account) {
    // Get the new message from SHAD API
    if (isset($update['data_enc'])) {
        $message = $update['data_enc'];
        foreach ($message['message_updates'] as $value) {
            $messageContent = $value['message'];
            $type = $messageContent['type'];
            $author_type = $messageContent['author_type'];
            $author_object_guid = $messageContent['author_object_guid'];
            if ($type == 'Text') {
                $text = (string)$messageContent['text'];
                // Write the message to a temporary file
                var_dump($value);
                file_put_contents('shad_messages.txt', $text . PHP_EOL, FILE_APPEND);
            }
        }
    }
});

// Simulating a long-running listener for SHAD API
while (true) {
    // You would use the actual SHAD API update check here
    sleep(1); // Sleep to avoid CPU hogging
}
