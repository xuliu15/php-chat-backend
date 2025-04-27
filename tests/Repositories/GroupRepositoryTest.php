<?php

namespace Tests\Repositories;

use App\Repositories\GroupRepository;
use App\Database;
use PHPUnit\Framework\TestCase;

class GroupRepositoryTest extends TestCase
{
    private $groupRepository;
    private $database;

    protected function setUp(): void
    {
        $this->database = new Database();
        $this->groupRepository = new GroupRepository($this->database);
        
        // Clean up the database before each test
        $pdo = $this->database->getConnection();
        $pdo->exec("DELETE FROM messages");
        $pdo->exec("DELETE FROM users");
        $pdo->exec("DELETE FROM groups");
        
        // Create test user
        $pdo->exec("INSERT INTO users (username) VALUES ('testuser')");
    }

    public function testCreateGroup()
    {
        $result = $this->groupRepository->createGroup('testgroup');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('testgroup', $result['name']);
    }

    public function testGetGroups()
    {
        // First create a group
        $this->groupRepository->createGroup('testgroup');

        // Then get all groups
        $groups = $this->groupRepository->getGroups();

        $this->assertIsArray($groups);
        $this->assertCount(1, $groups);
        $this->assertEquals('testgroup', $groups[0]['name']);
    }

    public function testGetGroupById()
    {
        // First create a group
        $createdGroup = $this->groupRepository->createGroup('testgroup');

        // Then get the group by ID
        $group = $this->groupRepository->getGroupById($createdGroup['id']);

        $this->assertIsArray($group);
        $this->assertEquals('testgroup', $group['name']);
    }

    public function testGetNonExistentGroupById()
    {
        $group = $this->groupRepository->getGroupById(999);
        $this->assertFalse($group);
    }

    public function testJoinGroup()
    {
        // First create a group
        $createdGroup = $this->groupRepository->createGroup('testgroup');

        // Then join the group
        $result = $this->groupRepository->joinGroup($createdGroup['id'], 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user_id', $result);
        $this->assertArrayHasKey('group_id', $result);
        $this->assertEquals(1, $result['user_id']);
        $this->assertEquals($createdGroup['id'], $result['group_id']);
    }

    public function testJoinGroupTwice()
    {
        // First create a group
        $createdGroup = $this->groupRepository->createGroup('testgroup');

        // Join the group first time
        $this->groupRepository->joinGroup($createdGroup['id'], 1);

        // Try to join again
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User is already a member of this group');
        $this->groupRepository->joinGroup($createdGroup['id'], 1);
    }
} 