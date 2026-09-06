<?php
namespace App\Controller;
use App\Repository\UserRepository;

class UserController {
    public function __construct(private UserRepository $userRepo) {}

    public function index(): void {
        if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            die('LỖI 403: CẢNH BÁO BẢO MẬT! Bạn không có quyền truy cập trang Quản trị viên.');
        }

        $errors = [];
        $success_msg = "";
        $u = ""; $n = ""; $r = "staff";
        
        $tab = $_GET['tab'] ?? 'internal';

        if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
            $this->userRepo->approveStudent((int)$_GET['id']);
            header("Location: index.php?route=users&tab=students&msg=approved");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_multiple') {
            $ids = $_POST['student_ids'] ?? [];
            if (!empty($ids) && is_array($ids)) {
                $ids = array_map('intval', $ids);
                $this->userRepo->approveMultipleStudents($ids);
                header("Location: index.php?route=users&tab=students&msg=bulk_approved");
                exit;
            } else {
                header("Location: index.php?route=users&tab=students&msg=no_selection");
                exit;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $this->userRepo->deleteUser((int)$_GET['id']);
            header("Location: index.php?route=users&tab=" . $tab . "&msg=deleted");
            exit;
        }

        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'approved') $success_msg = "Phê duyệt tài khoản thành công!";
            if ($_GET['msg'] == 'bulk_approved') $success_msg = "Đã phê duyệt hàng loạt tài khoản thành công!";
            if ($_GET['msg'] == 'deleted') $success_msg = "Xóa tài khoản thành công!";
            if ($_GET['msg'] == 'no_selection') $errors['general'] = "Vui lòng chọn ít nhất 1 sinh viên để duyệt!";
        }

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

        $internals = $this->userRepo->findInternals();

        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['p'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalStudents = $this->userRepo->countStudents($search);
        $totalPages = ceil($totalStudents / $limit);
        $students = $this->userRepo->findStudentsPaginated($search, $limit, $offset);

        require_once __DIR__ . '/../View/admin/quan_ly_user.php';
    }
}