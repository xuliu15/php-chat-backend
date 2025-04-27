<?php

namespace App\Repositories;

use App\Database;
use PDO;
use stdClass;

class MessageRepository {
    public function __construct(private Database $database) {

    }
    public function createMessage(array $data): array {
        $sql = "INSERT INTO messages (content, user_id, group_id) VALUES (:content, :user_id, :group_id)";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':content', $data['content'], PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $data['userId'], PDO::PARAM_INT);
        $stmt->bindValue(':group_id', $data['groupId'], PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'id' => $pdo->lastInsertId(),
            'content' => $data['content'],
            'user_id' => $data['userId'],
            'group_id' => $data['groupId']
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