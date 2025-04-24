<?php
// tests/ChatTest.php

namespace Tests\Chat;

use App\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new Database('test.db');
        // Clean up existing data
        $this->db->query("DELETE FROM messages");
        $this->db->query("DELETE FROM users");
        $this->db->query("DELETE FROM groups");
    }

    public function testCreateUser()
    {
        $username = 'testuser_' . time();
        $this->db->query("INSERT INTO users (username) VALUES (?)", [$username]);
        
        $user = $this->db->query("SELECT username FROM users WHERE username = ?", [$username])->fetch();
        $this->assertEquals($username, $user['username']);
    }

    public function testCreateGroup()
    {
        $groupName = 'testgroup_' . time();
        $this->db->query("INSERT INTO groups (name) VALUES (?)", [$groupName]);
        
        $group = $this->db->query("SELECT name FROM groups WHERE name = ?", [$groupName])->fetch();
        $this->assertEquals($groupName, $group['name']);
    }

    public function testSendMessage()
    {
        // Create user and group first
        $username = 'messageuser_' . time();
        $groupName = 'messagegroup_' . time();
        
        $this->db->query("INSERT INTO users (username) VALUES (?)", [$username]);
        $this->db->query("INSERT INTO groups (name) VALUES (?)", [$groupName]);
        
        // Get IDs
        $user = $this->db->query("SELECT id FROM users WHERE username = ?", [$username])->fetch();
        $group = $this->db->query("SELECT id FROM groups WHERE name = ?", [$groupName])->fetch();
        
        // Send message
        $message = "Test message";
        $this->db->query(
            "INSERT INTO messages (user_id, group_id, content) VALUES (?, ?, ?)",
            [$user['id'], $group['id'], $message]
        );
        
        // Verify message
        $result = $this->db->query("SELECT content FROM messages WHERE user_id = ?", [$user['id']])->fetch();
        $this->assertEquals($message, $result['content']);
    }
}