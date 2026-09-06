<?php
namespace App\Repository;
use PDO;

class UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm cũ giữ lại để dự phòng
    public function findStudents(): array {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY (status = 'pending') DESC, id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // TỐI ƯU UX: Đếm tổng số sinh viên để phân trang
    public function countStudents(string $search = ''): int {
        $sql = "SELECT COUNT(id) FROM users WHERE role = 'student'";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (username LIKE ? OR full_name LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    // TỐI ƯU UX: Lấy sinh viên theo trang và từ khóa tìm kiếm
    public function findStudentsPaginated(string $search = '', int $limit = 10, int $offset = 0): array {
        $sql = "SELECT * FROM users WHERE role = 'student'";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (username LIKE ? OR full_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY (status = 'pending') DESC, id DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
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

    // TỐI ƯU UX: Phê duyệt hàng loạt sinh viên cùng lúc
    public function approveMultipleStudents(array $ids): bool {
        if (empty($ids)) return false;
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "UPDATE users SET status = 'active' WHERE id IN ($in) AND role = 'student'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($ids);
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