<?php
$thietbi = [];

// Hàm phân loại trạng thái
function phanLoai($trangthai) {
    if ($trangthai == "Hoạt động") {
        return "Đang sử dụng";
    } elseif ($trangthai == "Hỏng") {
        return "Cần sửa chữa";
    } else {
        return "Đang bảo trì";
    }
}

if (isset($_POST["them"])) {
    $ten = $_POST["ten"];
    $loai = $_POST["loai"];
    $phong = $_POST["phong"];
    $trangthai = $_POST["trangthai"];

    $thietbi[] = [
        "ten" => $ten,
        "loai" => $loai,
        "phong" => $phong,
        "trangthai" => $trangthai
    ];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý thiết bị</title>

    <style>
        /* 1. Nền sáng, chữ tối */
        body {
            background-color: #f4f7f9;
            color: #333;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 40px 0;
        }

        * {
            box-sizing: border-box;
        }

        /* 2. Card trắng bo góc */
        .card-custom {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* 3. Màu xanh trường */
        :root {
            --hnmu-blue: #003399;
        }

        .btn-primary-custom {
            background-color: var(--hnmu-blue);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #002266;
        }

        /* 4. Tiêu đề */
        h2, h3 {
            color: var(--hnmu-blue);
        }

        h2 {
            text-align: center;
            margin-block-end: 25px;
        }

        h3 {
            margin-block-end: 15px;
        }

        /* Form */
        .container {
            inline-size: 700px;
            max-inline-size: 95%;
            margin: auto;
            padding: 30px;
        }

        .form-group {
            margin-block-end: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-block-end: 6px;
        }

        input,
        select {
            inline-size: 100%;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-size: 15px;
        }

        /* Hiệu ứng khi nhập */
        input:focus,
        select:focus {
            outline: none;
            border-color: var(--hnmu-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 153, 0.15);
        }

        /* Nút */
        button {
            inline-size: 100%;
            padding: 11px;
            margin-block-start: 5px;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
        }

        /* Đường kẻ */
        hr {
            margin: 30px 0;
            border: none;
            border-block-start: 1px solid #dee2e6;
        }

        /* 5. Bảng */
        .table-custom {
            inline-size: 100%;
            border-collapse: collapse;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
        }

        .table-custom thead {
            background-color: var(--hnmu-blue);
            color: white;
        }
    </style>
</head>

<body>

<div class="container card-custom">

    <h2>QUẢN LÝ THIẾT BỊ PHÒNG LAB</h2>

    <form method="post">

        <div class="form-group">
            <label>Tên thiết bị</label>
            <input type="text"
                   name="ten"
                   placeholder="Nhập tên thiết bị"
                   required>
        </div>

        <div class="form-group">
            <label>Loại thiết bị</label>
            <input type="text"
                   name="loai"
                   placeholder="Ví dụ: Máy tính"
                   required>
        </div>

        <div class="form-group">
            <label>Phòng</label>
            <input type="text"
                   name="phong"
                   placeholder="Ví dụ: P101"
                   required>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>

            <select name="trangthai">
                <option value="Hoạt động">Hoạt động</option>
                <option value="Hỏng">Hỏng</option>
                <option value="Đang bảo trì">Đang bảo trì</option>
            </select>
        </div>

        <button type="submit"
                name="them"
                class="btn-primary-custom">
            + Thêm thiết bị
        </button>

    </form>

    <hr>

    <h3>Danh sách thiết bị</h3>

    <table class="table-custom">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên thiết bị</th>
                <th>Loại</th>
                <th>Phòng</th>
                <th>Trạng thái</th>
                <th>Phân loại</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($thietbi as $i => $tb) { ?>

            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= $tb["ten"] ?></td>
                <td><?= $tb["loai"] ?></td>
                <td><?= $tb["phong"] ?></td>
                <td><?= $tb["trangthai"] ?></td>
                <td><?= phanLoai($tb["trangthai"]) ?></td>
            </tr>

            <?php } ?>
        </tbody>
    </table>

</div>

</body>
</html>