<?php
// Bot Configuration

return [
    'telegram' => [
        'token' => 'YOUR_BOT_TOKEN_HERE',
        'webhook_url' => 'https://yourdomain.com/public/index.php'
    ],
    'database' => [
        'host' => 'localhost',
        'name' => 'sport_events_bot',
        'user' => 'your_db_user',
        'pass' => 'your_db_password'
    ],
    'default_language' => 'en' // Default language
];