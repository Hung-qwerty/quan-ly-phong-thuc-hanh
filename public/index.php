<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repository\UserRepository;
use App\Controller\UserController;
use App\Repository\DeviceMaintenanceRepository;
use App\Controller\DeviceMaintenanceController;
use App\Repository\RoomBookingRepository;
use App\Controller\RoomBookingController;

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

case 'devices':
        $devMaintRepo = new DeviceMaintenanceRepository($pdo);
        $devMaintController = new DeviceMaintenanceController($devMaintRepo);
        $devMaintController->indexDevices();
        break;

    case 'maintenance':
        $devMaintRepo = new DeviceMaintenanceRepository($pdo);
        $devMaintController = new DeviceMaintenanceController($devMaintRepo);
        $devMaintController->indexMaintenance();
        break;

    default:
        http_response_code(404);
        echo "404 - Không tìm thấy trang yêu cầu!";
        break;
}