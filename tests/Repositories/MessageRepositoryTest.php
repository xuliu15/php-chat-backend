<?php

namespace Tests\Repositories;

use App\Repositories\MessageRepository;
use App\Database;
use PHPUnit\Framework\TestCase;

class MessageRepositoryTest extends TestCase
{
    private $messageRepository;
    private $database;

    protected function setUp(): void
    {
        $this->database = new Database();
        $this->messageRepository = new MessageRepository($this->database);
        
        // Clean up the database before each test
        $pdo = $this->database->getConnection();
        $pdo->exec("DELETE FROM messages");
        $pdo->exec("DELETE FROM users");
        $pdo->exec("DELETE FROM groups");
        
        // Create test user and group
        $pdo->exec("INSERT INTO users (username) VALUES ('testuser')");
        $pdo->exec("INSERT INTO groups (name) VALUES ('testgroup')");
    }

    public function testCreateMessage()
    {
        $messageData = [
            'userId' => 1,
            'groupId' => 1,
            'content' => 'Test message'
        ];

        $result = $this->messageRepository->createMessage($messageData);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('group_id', $result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals(1, $result['group_id']);
        $this->assertEquals(1, $result['user_id']);
        $this->assertEquals('Test message', $result['content']);
    }

    public function testGetMessagesByGroupId()
    {
        // First create a message
        $messageData = [
            'userId' => 1,
            'groupId' => 1,
            'content' => 'Test message'
        ];
        $this->messageRepository->createMessage($messageData);

        // Then get messages
        $messages = $this->messageRepository->getMessagesbyGroupId(1);

        $this->assertIsArray($messages);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test message', $messages[0]['content']);
    }

    public function testGetMessagesByNonExistentGroupId()
    {
        $messages = $this->messageRepository->getMessagesbyGroupId(999);
        $this->assertIsArray($messages);
        $this->assertEmpty($messages);
    }
} 