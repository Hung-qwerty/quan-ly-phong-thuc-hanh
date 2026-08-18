<?php
// login.php - Trang cổng thông tin / điều hướng bài nhóm
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cổng Thông Tin - Quản Lý Phòng Thực Hành (Nhóm)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f9;
            color: #333;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .card-custom {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        :root {
            --hnmu-blue: #003399;
        }
        .header-banner {
            background: linear-gradient(135deg, #003399, #004dc6);
            color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 25px;
        }
        .btn-role {
            background-color: #fff;
            color: var(--hnmu-blue);
            border: 2px solid var(--hnmu-blue);
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-role:hover {
            background-color: var(--hnmu-blue);
            color: white;
        }
        .list-group-item {
            border-left: 4px solid var(--hnmu-blue);
            margin-bottom: 8px;
            border-radius: 6px !important;
        }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 950px;">
        
        <!-- Banner phong cách trường -->
        <div class="header-banner text-center shadow-sm">
            <h2 class="fw-bold mb-2">CỔNG THÔNG TIN QUẢN LÝ PHÒNG THỰC HÀNH</h2>
            <p class="mb-0 text-white-50">Hệ thống bài tập nhóm - Đại học Thủ đô Hà Nội (HNMU)</p>
        </div>

        <!-- Khung điều hướng nhanh các module của thành viên -->
        <div class="row g-4">
            
            <!-- Phân hệ Admin (TV1) -->
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="text-primary fw-bold mb-3">🛠️ Phân hệ Admin / TV1</h5>
                    <p class="text-muted small">Quản lý hệ thống, tài khoản và phân quyền người dùng.</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Quản lý User (Validation Buổi 3)</span>
                            <a href="/quan-ly-phong-thuc-hanh/pages/admin/quan_ly_user.php" class="btn btn-sm btn-role">Truy cập</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Phân hệ Staff (TV4, TV5) -->
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="text-warning fw-bold mb-3 text-dark">📋 Phân hệ Cán bộ / Lab (TV4, TV5)</h5>
                    <p class="text-muted small">Quản lý thiết bị, bảo trì và kiểm tra cơ sở vật chất.</p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Thêm thiết bị mới (TV4)</span>
                            <a href="/quan-ly-phong-thuc-hanh/pages/staff/themthietbitv4.php" class="btn btn-sm btn-role">Truy cập</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Form báo trì thiết bị (TV5)</span>
                            <a href="/quan-ly-phong-thuc-hanh/pages/staff/formbaotritv5.php" class="btn btn-sm btn-role">Truy cập</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Phân hệ Student (TV2, TV3) -->
            <div class="col-md-12">
                <div class="card card-custom p-4">
                    <h5 class="text-success fw-bold mb-3">🎓 Phân hệ Sinh viên (TV2, TV3)</h5>
                    <p class="text-muted small">Tra cứu phòng thực hành, đặt lịch và sử dụng dịch vụ sinh viên.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Quản lý phòng thực hành (TV2)</span>
                                    <a href="/quan-ly-phong-thuc-hanh/pages/student/TV2-quanlyphongthuchanh.php" class="btn btn-sm btn-role">Truy cập</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Quản lý Booking / Đặt lịch (TV3)</span>
                                    <a href="/quan-ly-phong-thuc-hanh/pages/student/TV3-quanlybooking.php" class="btn btn-sm btn-role">Truy cập</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-4 text-muted small">
            <p>Hệ thống phát triển bởi Nhóm đồ án - Ngành Công nghệ thông tin HNMU</p>
        </div>

    </div>
</body>
</html>