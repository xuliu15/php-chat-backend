<?php
namespace App;

use PDO;
use PDOException;

class Database {
    public function getConnection(): PDO
    {
        $pdo = new PDO(
            "sqlite:" . __DIR__ . '/../../data/chat.db', 
            null, 
            null, 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Create tables if they don't exist
        $this->initTables($pdo);
        
        return $pdo;
    }

    private function initTables(PDO $pdo): void
    {
        $queries = [
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE
            )",
            
            // Groups table
            "CREATE TABLE IF NOT EXISTS groups (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE
            )",
            
            // Group members table
            "CREATE TABLE IF NOT EXISTS group_members (
                user_id INTEGER,
                group_id INTEGER,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (group_id) REFERENCES groups(id),
                UNIQUE(user_id, group_id)
            )",
            
            // Messages table
            "CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                group_id INTEGER,
                user_id INTEGER,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (group_id) REFERENCES groups(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )"
        ];

        foreach ($queries as $query) {
            $pdo->exec($query);
        }
    }
} 