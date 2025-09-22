<?php

class TelegramService {
    private $token;
    private $apiUrl;

    public function __construct($token) {
        $this->token = $token;
        $this->apiUrl = "https://api.telegram.org/bot{$token}/";
    }

    public function sendMessage($chat_id, $text, $reply_markup = null) {
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];

        if ($reply_markup) {
            $data['reply_markup'] = json_encode($reply_markup);
        }

        return $this->makeRequest('sendMessage', $data);
    }

    public function sendMenu($chat_id, $text, $menu_items) {
        $keyboard = [];
        foreach (array_chunk($menu_items, 2) as $row) {
            $keyboard[] = $row;
        }

        $reply_markup = [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];

        return $this->sendMessage($chat_id, $text, $reply_markup);
    }

    private function makeRequest($method, $data) {
        $url = $this->apiUrl . $method;
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    public function getWebhookUpdate() {
        return json_decode(file_get_contents('php://input'), true);
    }
}