<?php

class Bot {
    private $telegram;
    private $userModel;
    private $eventModel;
    private $registrationModel;
    private $lang;

    public function __construct($config) {
        $database = Database::getInstance($config['database']);
        $this->telegram = new TelegramService($config['telegram']['token']);
        
        $this->userModel = new User($database);
        $this->eventModel = new Event($database);
        $this->registrationModel = new Registration($database);
        $this->lang = new LanguageService($config['default_language']);
    }

    public function handleUpdate($update) {
        // Set user language if available
        $this->setUserLanguage($update);
        
        if (isset($update['message']['text'])) {
            $this->handleMessage($update);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallback($update);
        }
    }

    private function setUserLanguage($update) {
        $user_id = null;
        
        if (isset($update['message']['from']['id'])) {
            $user_id = $update['message']['from']['id'];
        } elseif (isset($update['callback_query']['from']['id'])) {
            $user_id = $update['callback_query']['from']['id'];
        }
        
        if ($user_id) {
            $user = $this->userModel->findByTelegramId($user_id);
            if ($user && !empty($user['language'])) {
                $this->lang->setLanguage($user['language']);
            }
        }
    }

    private function handleMessage($update) {
        $text = $update['message']['text'];
        $chat_id = $update['message']['from']['id'];

        switch (trim($text)) {
            case '/start':
                $command = new StartCommand($this->telegram, $this->userModel, $this->lang);
                $command->handle($update);
                break;
                
            case $this->lang->get('menu_events'):
            case '/events':
                $command = new EventsCommand($this->telegram, $this->eventModel, $this->lang);
                $command->handle($update);
                break;
                
            case $this->lang->get('menu_my_registrations'):
                // TODO: Implement registration view
                $this->telegram->sendMessage($chat_id, "Feature in development...");
                break;
                
            case $this->lang->get('menu_help'):
            case '/help':
                $command = new HelpCommand($this->telegram, $this->lang);
                $command->handle($update);
                break;
                
            case $this->lang->get('menu_language'):
                $this->showLanguageMenu($chat_id);
                break;
                
            default:
                $this->telegram->sendMessage($chat_id, "Unknown command. Use menu for navigation.");
        }
    }

    private function handleCallback($update) {
        $callback_data = $update['callback_query']['data'];
        $chat_id = $update['callback_query']['from']['id'];

        // Handle registration
        if (strpos($callback_data, $this->lang->get('callback_register_prefix')) === 0) {
            $event_id = str_replace($this->lang->get('callback_register_prefix'), '', $callback_data);
            $this->handleRegistration($chat_id, $event_id);
        }
        // Handle language change
        elseif (strpos($callback_data, 'lang_') === 0) {
            $language = str_replace('lang_', '', $callback_data);
            $this->changeUserLanguage($chat_id, $language);
        }
    }

    private function handleRegistration($chat_id, $event_id) {
        $user = $this->userModel->findByTelegramId($chat_id);
        if (!$user) {
            $this->telegram->sendMessage($chat_id, $this->lang->get('error') . ": User not found.");
            return;
        }

        $event = $this->eventModel->findById($event_id);
        if (!$event || $event['status'] !== 'active') {
            $this->telegram->sendMessage($chat_id, "Event not found or unavailable.");
            return;
        }

        // Check if already registered
        if ($this->registrationModel->isUserRegistered($user['id'], $event_id)) {
            $this->telegram->sendMessage($chat_id, $this->lang->get('register_already'));
            return;
        }

        // Check if spots available
        if ($event['registered_count'] >= $event['max_participants']) {
            $this->telegram->sendMessage($chat_id, $this->lang->get('register_full'));
            return;
        }

        // Register user
        if ($this->registrationModel->registerUserForEvent($user['id'], $event_id)) {
            $date = date('d.m.Y', strtotime($event['date']));
            $text = $this->lang->get('register_success') . "\n\n";
            $text .= "🏆 {$event['title']}\n";
            $text .= "{$this->lang->get('events_date')} {$date}\n";
            $text .= "{$this->lang->get('events_location')} {$event['location']}\n\n";
            $text .= "See you at the competition! 🏆";

            $this->telegram->sendMessage($chat_id, $text);
        } else {
            $this->telegram->sendMessage($chat_id, $this->lang->get('register_error'));
        }
    }

    private function showLanguageMenu($chat_id) {
        $languages = LanguageService::getSupportedLanguages();
        $keyboard = [];

        foreach ($languages as $code => $name) {
            $keyboard[] = [['text' => $name, 'callback_data' => "lang_{$code}"]];
        }

        $reply_markup = ['inline_keyboard' => $keyboard];
        $this->telegram->sendMessage($chat_id, $this->lang->get('language_choose'), $reply_markup);
    }

    private function changeUserLanguage($chat_id, $language) {
        $supported_languages = array_keys(LanguageService::getSupportedLanguages());
        
        if (!in_array($language, $supported_languages)) {
            $this->telegram->sendMessage($chat_id, "Unsupported language.");
            return;
        }

        // Update user language in database
        $this->userModel->updateLanguage($chat_id, $language);
        
        // Update current language service
        $this->lang->setLanguage($language);
        
        // Send confirmation message
        $message = $language === 'en' ? 'Language changed to English! 🇬🇧' : 'Язык изменен на русский! 🇷🇺';
        $this->telegram->sendMessage($chat_id, $message);
        
        // Show main menu with new language
        $menu_items = [
            [$this->lang->get('menu_my_registrations')],
            [$this->lang->get('menu_events')],
            [$this->lang->get('menu_help')],
            [$this->lang->get('menu_language')]
        ];

        $text = $this->lang->get('welcome') . "!\n\n" . $this->lang->get('start_description');
        $this->telegram->sendMenu($chat_id, $text, $menu_items);
    }
}