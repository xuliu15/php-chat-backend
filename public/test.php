<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Database;

// Initialize database
$db = new Database();

try {
    // Check if user exists first
    $existingUser = $db->query("SELECT id FROM users WHERE username = ?", ["john"])->fetch();
    if (!$existingUser) {
        // Create a new user only if it doesn't exist
        $db->query("INSERT INTO users (username) VALUES (?)", ["john"]);
        echo "User 'john' created successfully\n";
    } else {
        echo "User 'john' already exists\n";
    }

    // Check if group exists
    $existingGroup = $db->query("SELECT id FROM groups WHERE name = ?", ["general"])->fetch();
    if (!$existingGroup) {
        // Create a new group only if it doesn't exist
        $db->query("INSERT INTO groups (name) VALUES (?)", ["general"]);
        echo "Group 'general' created successfully\n";
    } else {
        echo "Group 'general' already exists\n";
    }

    // Send a new message (we can send multiple messages, no unique constraint here)
    $db->query(
        "INSERT INTO messages (user_id, group_id, content) VALUES (?, ?, ?)",
        [1, 1, "Hello, world! " . date('Y-m-d H:i:s')]  // Add timestamp to make each message unique
    );
    echo "Message sent successfully\n";

    // Let's verify the data by selecting and displaying it
    echo "\nChecking database contents:\n";
    
    // Check users
    echo "\nUsers in database:\n";
    $users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);

    // Check groups
    echo "\nGroups in database:\n";
    $groups = $db->query("SELECT * FROM groups")->fetchAll(PDO::FETCH_ASSOC);
    print_r($groups);

    // Check messages
    echo "\nMessages in database:\n";
    $messages = $db->query("SELECT * FROM messages")->fetchAll(PDO::FETCH_ASSOC);
    print_r($messages);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}