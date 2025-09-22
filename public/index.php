<?php
// Entry Point for Telegram Webhook

require_once '../config/config.php';
require_once '../database/Database.php';
require_once '../src/Models/User.php';
require_once '../src/Models/Event.php';
require_once '../src/Models/Registration.php';
require_once '../src/Services/TelegramService.php';
require_once '../src/Commands/StartCommand.php';
require_once '../src/Commands/EventsCommand.php';
require_once '../src/Commands/RegisterCommand.php';
require_once '../src/Bot.php';

$config = require_once '../config/config.php';

try {
    $bot = new Bot($config);
    $update = json_decode(file_get_contents('php://input'), true);
    
    if ($update) {
        $bot->handleUpdate($update);
    }
} catch (Exception $e) {
    error_log("Bot Error: " . $e->getMessage());
}