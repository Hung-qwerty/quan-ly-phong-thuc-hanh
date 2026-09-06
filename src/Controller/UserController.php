<?php
namespace App\Controller;
use App\Repository\UserRepository;

class UserController {
    public function __construct(private UserRepository $userRepo) {}

    public function index(): void {
        $errors = [];
        $success_msg = "";
        $u = ""; $n = ""; $r = "staff";
        
        $tab = $_GET['tab'] ?? 'internal';

        // Xử lý duyệt sinh viên
        if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
            $this->userRepo->approveStudent((int)$_GET['id']);
            header("Location: /quan-ly-phong-thuc-hanh/public/index.php?route=users&tab=students&msg=approved");
            exit;
        }

        // Xử lý xóa tài khoản
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $this->userRepo->deleteUser((int)$_GET['id']);
            header("Location: /quan-ly-phong-thuc-hanh/public/index.php?route=users&tab=" . $tab . "&msg=deleted");
            exit;
        }

        // Xử lý thông báo từ URL chuyển hướng
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'approved') $success_msg = "Phê duyệt tài khoản sinh viên thành công!";
            if ($_GET['msg'] == 'deleted') $success_msg = "Xóa tài khoản thành công!";
        }

        // Xử lý thêm tài khoản nội bộ qua POST
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_internal_user'])) {
            $u = trim($_POST['username'] ?? '');
            $n = trim($_POST['fullname'] ?? '');
            $r = $_POST['role'] ?? 'staff';
            $password_raw = trim($_POST['password'] ?? '');

            if (empty($u)) $errors['username'] = "Tên đăng nhập không được để trống!";
            if (empty($n)) $errors['fullname'] = "Họ tên không được để trống!";
            if (empty($password_raw)) $errors['password'] = "Mật khẩu không được để trống!";

            if (empty($errors)) {
                if ($this->userRepo->checkUsernameExists($u)) {
                    $errors['username'] = "Tên đăng nhập này đã tồn tại!";
                } else {
                    $hashed_pass = password_hash($password_raw, PASSWORD_DEFAULT);
                    $this->userRepo->createInternalUser([
                        'username' => $u,
                        'password' => $hashed_pass,
                        'fullname' => $n,
                        'role' => $r
                    ]);
                    $success_msg = "Thêm tài khoản nội bộ thành công!";
                    $u = $n = "";
                }
            }
        }

        // Lấy dữ liệu hiển thị cho các tab
        $students = $this->userRepo->findStudents();
        $internals = $this->userRepo->findInternals();

        // Gọi View hiển thị
        require_once __DIR__ . '/../View/admin/quan_ly_user.php';
    }
}