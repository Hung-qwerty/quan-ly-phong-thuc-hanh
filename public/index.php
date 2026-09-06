<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repository\UserRepository;
use App\Controller\UserController;
use App\Repository\MaintenanceRepository;
use App\Controller\MaintenanceController;
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
    case 'staff_bookings':
    case 'borrowings':

        require_once __DIR__ . '/../src/Repository/DeviceRepository.php';
        require_once __DIR__ . '/../src/Controller/DeviceController.php';

        $deviceRepo = new \App\Repository\DeviceRepository($pdo);

        $deviceController =
            new \App\Controller\DeviceController($deviceRepo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deviceController->handleRequest();
        } else {
            $deviceController->index();
        }

        break;

    case 'maintenance':

        require_once __DIR__ . '/../src/Repository/MaintenanceRepository.php';
        require_once __DIR__ . '/../src/Controller/MaintenanceController.php';

        $maintenanceRepo =
            new \App\Repository\MaintenanceRepository($pdo);

        $maintenanceController =
            new \App\Controller\MaintenanceController(
                $maintenanceRepo
            );

        $maintenanceController->index();

        break;

    default:

        http_response_code(404);

        echo "404 - Không tìm thấy trang yêu cầu!";

        break;
}