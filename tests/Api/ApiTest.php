<?php

use PHPUnit\Framework\TestCase;
use App\Database;

class ApiTest extends TestCase
{
    private $db;
    private $baseUrl = 'http://localhost:8080';

    protected function setUp(): void
    {
        $this->db = new Database('chat.db', false);  // Disable debug output
        // Clean up test data
        $this->db->query("DELETE FROM messages");
        $this->db->query("DELETE FROM users");
        $this->db->query("DELETE FROM groups");

        // Check if server is running
        $ch = curl_init($this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode === 0) {
            $this->markTestSkipped('API server is not running. Please start the server with `composer start`');
        }
    }

    public function testUserFlow()
    {
        // Create a user
        $userData = ['username' => 'testuser'];
        $response = $this->makeRequest('POST', '/users', $userData);
        
        $this->assertNotNull($response['body'], 'Response body should not be null');
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals('success', $response['body']['status']);
        $this->assertEquals('testuser', $response['body']['data']['username']);

        // Get all users
        $response = $this->makeRequest('GET', '/users');
        $this->assertNotNull($response['body'], 'Response body should not be null');
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals(1, count($response['body']['data']));
        $this->assertEquals('testuser', $response['body']['data'][0]['username']);
    }

    public function testGroupFlow()
    {
        // Create a group
        $groupData = ['name' => 'testgroup'];
        $response = $this->makeRequest('POST', '/groups', $groupData);
        
        $this->assertNotNull($response['body'], 'Response body should not be null');
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals('success', $response['body']['status']);
        $this->assertEquals('testgroup', $response['body']['data']['name']);

        // Get all groups
        $response = $this->makeRequest('GET', '/groups');
        $this->assertNotNull($response['body'], 'Response body should not be null');
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals(1, count($response['body']['data']));
        $this->assertEquals('testgroup', $response['body']['data'][0]['name']);
    }

    public function testMessageFlow()
    {
        // Create a user
        $userResponse = $this->makeRequest('POST', '/users', ['username' => 'testuser']);
        $this->assertNotNull($userResponse['body'], 'User response body should not be null');
        $userId = $userResponse['body']['data']['id'];

        // Create a group
        $groupResponse = $this->makeRequest('POST', '/groups', ['name' => 'testgroup']);
        $this->assertNotNull($groupResponse['body'], 'Group response body should not be null');
        $groupId = $groupResponse['body']['data']['id'];

        // Join the group first
        $joinResponse = $this->makeRequest('POST', "/groups/$groupId/join", [
            'user_id' => $userId
        ]);
        $this->assertEquals(200, $joinResponse['status_code']);

        // Send a message
        $messageData = [
            'user_id' => $userId,
            'group_id' => $groupId,
            'content' => 'Hello, world!'
        ];
        $response = $this->makeRequest('POST', '/messages', $messageData);
        
        $this->assertNotNull($response['body'], 'Message response body should not be null');
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals('success', $response['body']['status']);
        $this->assertEquals('Hello, world!', $response['body']['data']['content']);

        // Get group messages
        $response = $this->makeRequest('GET', "/groups/$groupId/messages");
        $this->assertNotNull($response['body'], 'Get messages response body should not be null');
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals(1, count($response['body']['data']));
        $this->assertEquals('Hello, world!', $response['body']['data'][0]['content']);
        $this->assertEquals('testuser', $response['body']['data'][0]['username']);
    }

    public function testJoinGroup()
    {
        // Create a user
        $userResponse = $this->makeRequest('POST', '/users', ['username' => 'testuser']);
        $userId = $userResponse['body']['data']['id'];

        // Create a group
        $groupResponse = $this->makeRequest('POST', '/groups', ['name' => 'testgroup']);
        $groupId = $groupResponse['body']['data']['id'];

        // Join group
        $response = $this->makeRequest('POST', "/groups/$groupId/join", [
            'user_id' => $userId
        ]);
        
        $this->assertEquals(200, $response['status_code']);
        $this->assertEquals('success', $response['body']['status']);

        // Check members
        $response = $this->makeRequest('GET', "/groups/$groupId/members");
        $this->assertEquals(200, $response['status_code']);
        $this->assertCount(1, $response['body']['data']);
        $this->assertEquals('testuser', $response['body']['data'][0]['username']);
    }

    private function makeRequest($method, $path, $data = null)
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->fail("cURL request failed: $error");
        }

        $body = json_decode($response, true);
        if ($body === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->fail("Invalid JSON response: " . json_last_error_msg() . "\nResponse: " . $response);
        }

        return [
            'status_code' => $statusCode,
            'body' => $body
        ];
    }
}