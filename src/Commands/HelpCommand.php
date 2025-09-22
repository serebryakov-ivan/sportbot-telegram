<?php

class HelpCommand {
    private $telegram;
    private $lang;

    public function __construct($telegram, $languageService) {
        $this->telegram = $telegram;
        $this->lang = $languageService;
    }

    public function handle($update) {
        $chat_id = $update['message']['from']['id'];

        $text = $this->lang->get('help_title') . "\n\n";
        $text .= $this->lang->get('help_description') . "\n";
        $text .= $this->lang->get('help_feature1') . "\n";
        $text .= $this->lang->get('help_feature2') . "\n";
        $text .= $this->lang->get('help_feature3') . "\n\n";
        $text .= $this->lang->get('help_navigation');

        $this->telegram->sendMessage($chat_id, $text);
    }
}