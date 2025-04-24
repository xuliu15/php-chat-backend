<?php
// src/Database.php

namespace App;

use PDO;
use PDOException;

class Database {
    private $pdo;
    private $dbPath;
    private $debug;

    public function __construct($dbName = 'chat.db', $debug = true) {
        $this->debug = $debug;
        // Set the database path
        $this->dbPath = __DIR__ . '/../data/' . $dbName . '.db';
        
        try {
            // Debug information
            $this->log("Trying to create/connect to database at: " . $this->dbPath);
            
            // Make sure the directory exists
            $dbDir = dirname($this->dbPath);
            if (!is_dir($dbDir)) {
                $this->log("Creating directory: " . $dbDir);
                mkdir($dbDir, 0777, true);
            }

            // Check directory permissions
            $this->log("Directory exists: " . (is_dir($dbDir) ? 'yes' : 'no'));
            $this->log("Directory writable: " . (is_writable($dbDir) ? 'yes' : 'no'));

            // Connect to SQLite database
            $this->pdo = new PDO('sqlite:' . $this->dbPath);
            
            // Set error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables if they don't exist
            $this->initTables();
            
            $this->log("Database connection successful!");
            $this->log("Database file exists: " . (file_exists($this->dbPath) ? 'yes' : 'no'));
            
        } catch(PDOException $e) {
            $this->log("Database path: " . $this->dbPath);
            $this->log("Connection failed: " . $e->getMessage());
            $this->log("Directory writable: " . (is_writable(dirname($this->dbPath)) ? 'yes' : 'no'));
            throw $e;
        }
    }

    private function log($message) {
        if ($this->debug) {
            echo $message . "\n";
        }
    }

    private function initTables() {
        // Create tables
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
            $this->pdo->exec($query);
        }
    }
    

    // Basic method to execute queries
    public function query($sql, $params = []) {
        if ($this->pdo === null) {
            throw new \RuntimeException("Database connection not established");
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Get database connection
    public function getConnection() {
        return $this->pdo;
    }
}
