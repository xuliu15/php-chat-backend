<?php

namespace App\Repositories;

use App\Database;
use PDO;
use stdClass;

class GroupRepository {

    public function __construct(private Database $database) {

    }

    public function createGroup(string $name): array {
        $sql = "INSERT INTO groups (name) VALUES (:name)";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        return ['id' => $pdo->lastInsertId(), 'name' => $name];
    }

    public function getGroups(): array {
        $pdo = $this->database->getConnection();
        $stmt=$pdo->query("SELECT * FROM groups");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGroupById(int $groupId){
        $sql = "SELECT * FROM groups WHERE id = :id";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function createMessage(int $groupId, int $userId, string $content): array {
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
        

    public function getGroupMessages(int $groupId): array {
        $sql = "SELECT m.*, u.username 
                FROM messages m 
                JOIN users u ON m.user_id = u.id 
                WHERE m.group_id = :group_id 
                ORDER BY m.created_at DESC";
                
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function joinGroup(int $groupId, int $userId): array {
        $pdo = $this->database->getConnection();
        
        $checkSql = "SELECT 1 FROM group_members WHERE user_id = :user_id AND group_id = :group_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $checkStmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetch()) {
            throw new \RuntimeException('User is already a member of this group');
        }
        
        $sql = "INSERT INTO group_members (user_id, group_id) VALUES (:user_id, :group_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'user_id' => $userId,
            'group_id' => $groupId
        ];
    }
}   