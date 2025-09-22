<?php

class EventsCommand {
    private $telegram;
    private $eventModel;
    private $lang;

    public function __construct($telegram, $eventModel, $languageService) {
        $this->telegram = $telegram;
        $this->eventModel = $eventModel;
        $this->lang = $languageService;
    }

    public function handle($update) {
        $chat_id = $update['message']['from']['id'];
        $events = $this->eventModel->getUpcomingEvents();

        if (empty($events)) {
            $this->telegram->sendMessage($chat_id, $this->lang->get('events_none'));
            return;
        }

        $text = $this->lang->get('events_title') . "\n\n";

        foreach ($events as $event) {
            $date = date('d.m.Y', strtotime($event['date']));
            $time = $event['time'] ? date('H:i', strtotime($event['time'])) : '';
            $spots_left = $event['max_participants'] - $event['registered_count'];

            $text .= "📅 <b>{$event['title']}</b>\n";
            $text .= "{$this->lang->get('events_date')} {$date}" . ($time ? " в {$time}" : "") . "\n";
            $text .= "{$this->lang->get('events_location')} {$event['location']}\n";
            $text .= "{$this->lang->get('events_participants')} {$event['registered_count']}/{$event['max_participants']}\n";
            $text .= "{$this->lang->get('events_spots_left')} {$spots_left}\n\n";
        }

        $text .= $this->lang->get('events_register_hint');

        $this->telegram->sendMessage($chat_id, $text);
    }
}