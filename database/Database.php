<?php

class Database {
    private $pdo;
    private static $instance = null;

    private function __construct($config) {
        try {
            $this->pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['name']};charset=utf8",
                $config['user'],
                $config['pass']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к БД: " . $e->getMessage());
        }
    }

    public static function getInstance($config = null) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}