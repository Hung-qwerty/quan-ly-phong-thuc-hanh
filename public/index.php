<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repository\UserRepository;
use App\Controller\UserController;

$route = $_GET['route'] ?? 'login';
$pdo = Database::connection();

// Điều hướng theo route trên URL
switch ($route) {
    case 'login':
        require_once __DIR__ . '/../src/View/auth/login.php';
        break;

    case 'register':
        require_once __DIR__ . '/../src/View/auth/register.php';
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: index.php?route=login');
        exit;

    // Phân hệ quản lý user của Hùng (TV1)
    case 'users':
        $userRepo = new UserRepository($pdo);
        $userController = new UserController($userRepo);
        $userController->index();
        break;

    // Phân hệ của TV2 (Phòng thực hành) - Ví dụ tích hợp từ pages cũ hoặc refactor sang Controller
    case 'rooms':
        // Gọi Controller phòng học hoặc require file tương ứng từ cấu trúc mới
        require_once __DIR__ . '/../src/View/student/TV2-quanlyphongthuchanh.php';
        break;

    // Phân hệ của TV3 (Booking)
    case 'bookings':
        require_once __DIR__ . '/../src/View/student/TV3-quanlybooking.php';
        break;

    // Phân hệ của TV4 (Thiết bị)
    case 'devices':
        require_once __DIR__ . '/../src/Repository/DeviceRepository.php';
        require_once __DIR__ . '/../src/Controller/DeviceController.php';

        $deviceRepo = new DeviceRepository($pdo);
        $deviceController = new DeviceController($deviceRepo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deviceController->handleRequest();
        } else {
            $deviceController->index();
        }
        break;

    // Phân hệ của TV5 (Bảo trì)
    case 'maintenance':
        require_once __DIR__ . '/../src/View/staff/baotritv5/bao_tri.php';
        break;

    default:
        http_response_code(404);
        echo "404 - Không tìm thấy trang yêu cầu!";
        break;
}