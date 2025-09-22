<?php

class RegisterCommand {
    private $telegram;
    private $eventModel;
    private $registrationModel;

    public function __construct($telegram, $eventModel, $registrationModel) {
        $this->telegram = $telegram;
        $this->eventModel = $eventModel;
        $this->registrationModel = $registrationModel;
    }

    public function handle($update) {
        $chat_id = $update['message']['from']['id'];
        $events = $this->eventModel->getUpcomingEvents();

        if (empty($events)) {
            $this->telegram->sendMessage($chat_id, "Нет доступных мероприятий для регистрации. 😔");
            return;
        }

        $text = "Выберите мероприятие для регистрации:\n\n";
        $keyboard = [];

        foreach ($events as $event) {
            $date = date('d.m', strtotime($event['date']));
            $button_text = "{$event['title']} ({$date})";
            $keyboard[] = [['text' => $button_text, 'callback_data' => "register_{$event['id']}"]];
        }

        $reply_markup = ['inline_keyboard' => $keyboard];
        $this->telegram->sendMessage($chat_id, $text, $reply_markup);
    }
}