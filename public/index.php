<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repository\UserRepository;
use App\Controller\UserController;

// Lấy route từ URL, mặc định nếu không truyền sẽ trỏ về danh sách user
$route = $_GET['route'] ?? 'users';

// Khởi tạo kết nối PDO dùng chung qua Database Core
$pdo = Database::connection();

// Router điều hướng đơn giản
switch ($route) {
    case 'users':
        $userRepo = new UserRepository($pdo);
        $userController = new UserController($userRepo);
        $userController->index();
        break;

    default:
        http_response_code(404);
        echo "404 - Không tìm thấy trang yêu cầu!";
        break;
}