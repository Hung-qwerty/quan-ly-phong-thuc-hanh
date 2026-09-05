<?php
namespace App\Repository;
use PDO;

class UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['username'], $data['password'], $data['full_name'], $data['role']]);
    }
}