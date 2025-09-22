<?php

class LanguageService {
    private $translations = [];
    private $currentLanguage = 'en';

    public function __construct($language = 'en') {
        $this->currentLanguage = $language;
        $this->loadTranslations($language);
    }

    private function loadTranslations($language) {
        $langFile = __DIR__ . '/../../lang/' . $language . '.php';
        if (file_exists($langFile)) {
            $this->translations = require_once $langFile;
        } else {
            // Fallback to English
            $this->translations = require_once __DIR__ . '/../../lang/en.php';
        }
    }

    public function get($key, $params = []) {
        $translation = $this->translations[$key] ?? $key;
        
        // Replace parameters if provided
        foreach ($params as $param => $value) {
            $translation = str_replace('{' . $param . '}', $value, $translation);
        }
        
        return $translation;
    }

    public function setLanguage($language) {
        $this->currentLanguage = $language;
        $this->loadTranslations($language);
    }

    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }

    public static function getSupportedLanguages() {
        return [
            'en' => 'English 🇬🇧',
            'ru' => 'Русский 🇷🇺'
        ];
    }
}