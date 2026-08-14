<?php
require_once 'config/database.php';

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        require 'pages/login.php';
        break;
    case 'student':
        require 'pages/student/dashboard.php';
        break;
    case 'staff':
        require 'pages/staff/dashboard.php';
        break;
    case 'admin':
        require 'pages/admin/dashboard.php';
        break;
    default:
        require 'pages/login.php';
        break;
}
?>