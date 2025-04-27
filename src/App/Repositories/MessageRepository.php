<?php

namespace App\Repositories;

use App\Database;
use PDO;
use stdClass;

class MessageRepository {
    public function __construct(private Database $database) {

    }
    public function createMessage(array $data): array {
        $pdo = $this->database->getConnection();
        
        $sql = "INSERT INTO messages (group_id, user_id, content) VALUES (:group_id, :user_id, :content)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':group_id', $data['groupId'], PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $data['userId'], PDO::PARAM_INT);
        $stmt->bindValue(':content', $data['content'], PDO::PARAM_STR);
        $stmt->execute();
        
        return [
            'id' => $pdo->lastInsertId(),
            'group_id' => $data['groupId'],
            'user_id' => $data['userId'],
            'content' => $data['content']
        ];
    }
    
    public function getMessagesbyGroupId(int $groupId): array {
        $sql = "SELECT * FROM messages WHERE group_id = :group_id";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}