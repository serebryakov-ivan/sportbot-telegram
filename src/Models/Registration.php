<?php

class Registration {
    private $db;

    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    public function registerUserForEvent($user_id, $event_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO registrations (user_id, event_id, status) 
                VALUES (?, ?, 'confirmed')
            ");
            return $stmt->execute([$user_id, $event_id]);
        } catch (PDOException $e) {
            // Если запись уже существует
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function getUserRegistrations($user_id) {
        $stmt = $this->db->prepare("
            SELECT r.*, e.title, e.date, e.time, e.location
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            WHERE r.user_id = ? AND r.status = 'confirmed'
            ORDER BY e.date ASC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isUserRegistered($user_id, $event_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM registrations 
            WHERE user_id = ? AND event_id = ? AND status = 'confirmed'
        ");
        $stmt->execute([$user_id, $event_id]);
        return $stmt->fetchColumn() > 0;
    }
}