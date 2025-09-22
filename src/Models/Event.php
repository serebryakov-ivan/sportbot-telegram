<?php

class Event {
    private $db;

    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    public function getAllActive() {
        $stmt = $this->db->prepare("
            SELECT *, 
                   (SELECT COUNT(*) FROM registrations WHERE event_id = events.id AND status = 'confirmed') as registered_count
            FROM events 
            WHERE status = 'active' 
            ORDER BY date ASC, time ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT *, 
                   (SELECT COUNT(*) FROM registrations WHERE event_id = events.id AND status = 'confirmed') as registered_count
            FROM events 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUpcomingEvents($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT *, 
                   (SELECT COUNT(*) FROM registrations WHERE event_id = events.id AND status = 'confirmed') as registered_count
            FROM events 
            WHERE status = 'active' AND date >= CURDATE()
            ORDER BY date ASC, time ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}