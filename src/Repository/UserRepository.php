<?php
namespace App\Repository;
use PDO;

class UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findStudents(): array {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY (status = 'pending') DESC, id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findInternals(): array {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE role IN ('admin', 'staff') ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveStudent(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function checkUsernameExists(string $username): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->rowCount() > 0;
    }

    public function createInternalUser(array $data): bool {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, full_name, role, status) VALUES (?, ?, ?, ?, 'active')");
        return $stmt->execute([$data['username'], $data['password'], $data['fullname'], $data['role']]);
    }
}