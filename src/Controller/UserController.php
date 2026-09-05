<?php
namespace App\Controller;
use App\Repository\UserRepository;

class UserController {
    public function __construct(private UserRepository $userRepo) {}

    public function index(): void {
        $users = $this->userRepo->findAll();
        // Gọi file view hiển thị danh sách
        require_once __DIR__ . '/../View/user/index.php';
    }
}