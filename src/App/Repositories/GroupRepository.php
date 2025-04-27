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

        
    
}   