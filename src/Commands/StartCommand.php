<?php

class StartCommand {
    private $telegram;
    private $userModel;
    private $lang;

    public function __construct($telegram, $userModel, $languageService) {
        $this->telegram = $telegram;
        $this->userModel = $userModel;
        $this->lang = $languageService;
    }

    public function handle($update) {
        $message = $update['message'];
        $chat_id = $message['from']['id'];
        $first_name = $message['from']['first_name'] ?? 'Participant';
        $username = $message['from']['username'] ?? null;
        $language = $this->lang->getCurrentLanguage();

        // Register user
        $this->userModel->create(
            $chat_id,
            $username,
            $first_name,
            $message['from']['last_name'] ?? null,
            $language
        );

        $menu_items = [
            [$this->lang->get('menu_my_registrations')],
            [$this->lang->get('menu_events')],
            [$this->lang->get('menu_help')],
            [$this->lang->get('menu_language')]
        ];

        $text = "<b>{$this->lang->get('welcome')}, {$first_name}! 👋</b>\n\n";
        $text .= $this->lang->get('start_welcome') . "\n";
        $text .= $this->lang->get('start_description');

        $this->telegram->sendMenu($chat_id, $text, $menu_items);
    }
}