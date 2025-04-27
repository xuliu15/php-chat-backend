<?php

namespace App\Repositories;

use App\Database;
use PDO;
use stdClass;

class UserRepository {
    public function __construct(private Database $database) {

    }

    public function createUser(string $username): array {
        $sql = "INSERT INTO users (username) VALUES (:username)";
        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return ['id' => $pdo->lastInsertId(), 'username' => $username];
    }

    public function getUsers(): array {
        $pdo = $this->database->getConnection();
        $stmt = $pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}