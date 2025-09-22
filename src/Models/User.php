<?php

class User {
    private $db;

    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    public function create($telegram_id, $username, $first_name, $last_name, $language = 'en') {
        $stmt = $this->db->prepare("
            INSERT INTO users (telegram_id, username, first_name, last_name, language) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            username = VALUES(username), 
            first_name = VALUES(first_name), 
            last_name = VALUES(last_name),
            language = VALUES(language)
        ");
        return $stmt->execute([$telegram_id, $username, $first_name, $last_name, $language]);
    }

    public function findByTelegramId($telegram_id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE telegram_id = ?");
        $stmt->execute([$telegram_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePhone($telegram_id, $phone) {
        $stmt = $this->db->prepare("UPDATE users SET phone = ? WHERE telegram_id = ?");
        return $stmt->execute([$phone, $telegram_id]);
    }

    public function updateLanguage($telegram_id, $language) {
        $stmt = $this->db->prepare("UPDATE users SET language = ? WHERE telegram_id = ?");
        return $stmt->execute([$language, $telegram_id]);
    }
}