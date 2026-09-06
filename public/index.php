<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repository\UserRepository;
use App\Controller\UserController;
use App\Repository\RoomBookingRepository;
use App\Controller\RoomBookingController;
use App\Repository\DeviceRepository;
use App\Controller\DeviceController;
use App\Repository\MaintenanceRepository;
use App\Controller\MaintenanceController;

$route = $_GET['route'] ?? $_GET['page'] ?? 'login';

$pdo = Database::connection();

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

    case 'users':
        $userRepo = new UserRepository($pdo);
        $userController = new UserController($userRepo);
        $userController->index();
        break;

    case 'bookings':
    case 'rooms':
        $bookingRepo = new RoomBookingRepository($pdo);
        $bookingController = new RoomBookingController($bookingRepo);
        $bookingController->index();
        break;

    // LUỒNG MỚI CỦA AN: QUẢN LÝ THIẾT BỊ & DUYỆT LỊCH
    case 'devices':
    case 'staff_bookings':
    case 'borrowings':
        $deviceRepo = new DeviceRepository($pdo);
        $deviceController = new DeviceController($deviceRepo);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deviceController->handleRequest();
        } else {
            $deviceController->index();
        }
        break;

    // LUỒNG MỚI CỦA AN: BẢO TRÌ
    case 'maintenance':
        $maintenanceRepo = new MaintenanceRepository($pdo);
        $maintenanceController = new MaintenanceController($maintenanceRepo);
        $maintenanceController->index();
        break;

    default:
        http_response_code(404);
        echo "404 - Không tìm thấy trang yêu cầu!";
        break;
}