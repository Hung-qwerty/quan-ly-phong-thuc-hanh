<?php

session_start();

require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../src/Repository/UserRepository.php';
require_once __DIR__ . '/../src/Controller/UserController.php';

require_once __DIR__ . '/../src/Repository/RoomBookingRepository.php';
require_once __DIR__ . '/../src/Controller/RoomBookingController.php';

use App\Repository\UserRepository;
use App\Controller\UserController;

use App\Repository\RoomBookingRepository;
use App\Controller\RoomBookingController;

$page = $_GET['page'] ?? 'users';

switch ($page) {

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
        $deviceController = new \App\Controller\DeviceController($deviceRepo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deviceController->handleRequest();
        } else {
            $deviceController->index();
        }

        break;


    case 'maintenance':

        require_once __DIR__ . '/../src/Repository/MaintenanceRepository.php';
        require_once __DIR__ . '/../src/Controller/MaintenanceController.php';

        $maintenanceRepo = new \App\Repository\MaintenanceRepository($pdo);
        $maintenanceController = new \App\Controller\MaintenanceController($maintenanceRepo);

        $maintenanceController->index();

        break;


    default:

        $userRepo = new UserRepository($pdo);
        $userController = new UserController($userRepo);
        $userController->index();

        break;
}