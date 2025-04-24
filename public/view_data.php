<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Database;

$db = new Database();

try {
    echo "\n=== USERS ===\n";
    $users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Username: {$user['username']}\n";
    }

    echo "\n=== GROUPS ===\n";
    $groups = $db->query("SELECT * FROM groups")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($groups as $group) {
        echo "ID: {$group['id']}, Name: {$group['name']}\n";
    }

    echo "\n=== MESSAGES ===\n";
    $messages = $db->query("
        SELECT m.*, u.username, g.name as group_name 
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        JOIN groups g ON m.group_id = g.id
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($messages as $message) {
        echo "ID: {$message['id']}, From: {$message['username']}, ";
        echo "Group: {$message['group_name']}, Message: {$message['content']}\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}