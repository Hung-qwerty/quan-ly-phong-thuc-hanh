<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Repository\UserRepository;
use App\Controller\UserController;

$route = $_GET['route'] ?? 'login';
$pdo = Database::connection();

switch ($route) {
    case 'login':
        require_once __DIR__ . '/../src/View/auth/login.php';
        break;

    case 'register':
         require_once __DIR__ . '/../src/View/auth/register.php';
    break;

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