<?php

// =====================================================
// 1. MẢNG PHÒNG THỰC HÀNH
// Có 4 trường dữ liệu:
// Mã phòng, Tên phòng, Sức chứa, Trạng thái
// =====================================================

$rooms = [
    [
        "ma_phong" => "P101",
        "ten_phong" => "Phòng máy 101",
        "suc_chua" => 40,
        "trang_thai" => "Trống"
    ],
    [
        "ma_phong" => "P102",
        "ten_phong" => "Phòng máy 102",
        "suc_chua" => 35,
        "trang_thai" => "Đang sử dụng"
    ],
    [
        "ma_phong" => "P103",
        "ten_phong" => "Phòng máy 103",
        "suc_chua" => 45,
        "trang_thai" => "Bảo trì"
    ]
];


// =====================================================
// 2. HÀM TỰ ĐỊNH NGHĨA
// Kiểm tra phòng có thể đặt hay không
// =====================================================

function kiemTraPhong($trang_thai)
{
    if ($trang_thai == "Trống") {
        return "Có thể đặt";
    } elseif ($trang_thai == "Đang sử dụng") {
        return "Không thể đặt";
    } else {
        return "Không thể đặt";
    }
}


// =====================================================
// 3. TIẾP NHẬN DỮ LIỆU TỪ FORM
// =====================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ma_phong = $_POST["ma_phong"];
    $ten_phong = $_POST["ten_phong"];
    $suc_chua = $_POST["suc_chua"];
    $trang_thai = $_POST["trang_thai"];

    // Thêm dữ liệu mới vào mảng
    $rooms[] = [
        "ma_phong" => $ma_phong,
        "ten_phong" => $ten_phong,
        "suc_chua" => $suc_chua,
        "trang_thai" => $trang_thai
    ];
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>Quản lý phòng thực hành</title>


    <!-- =================================================
         CSS ĐƠN GIẢN
    ================================================== -->

    <style>

        /* Phần nền của trang */
        body {
            background-color: #f4f7f9;
            color: #333;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
        }


        /* Khung chính */
        .container {
            width: 900px;
            max-width: 95%;
            margin: auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }


        /* Tiêu đề */
        h1 {
            text-align: center;
            color: #003399;
            margin-bottom: 10px;
        }

        h2 {
            color: #003399;
            margin-top: 30px;
        }


        /* Mô tả */
        .description {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }


        /* Form */
        form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }


        /* Ô nhập */
        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }


        /* Label */
        label {
            font-weight: bold;
        }


        /* Nút */
        button {
            background-color: #003399;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }


        button:hover {
            background-color: #002266;
        }


        /* Bảng */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }


        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }


        /* Tiêu đề bảng */
        th {
            background-color: #003399;
            color: white;
        }


        /* Dòng chẵn */
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

    </style>

</head>


<body>


<div class="container">

    <!-- =================================================
         TIÊU ĐỀ
    ================================================== -->

    <h1>QUẢN LÝ PHÒNG THỰC HÀNH</h1>

    <p class="description">
        Quản lý thông tin và trạng thái phòng thực hành
    </p>


    <!-- =================================================
         FORM NHẬP DỮ LIỆU
    ================================================== -->

    <h2>Thêm phòng thực hành</h2>

    <form method="POST">

        <label>Mã phòng</label>

        <input
            type="text"
            name="ma_phong"
            placeholder="Ví dụ: P104"
            required
        >


        <label>Tên phòng</label>

        <input
            type="text"
            name="ten_phong"
            placeholder="Ví dụ: Phòng máy 104"
            required
        >


        <label>Sức chứa</label>

        <input
            type="number"
            name="suc_chua"
            placeholder="Ví dụ: 40"
            min="1"
            required
        >


        <label>Trạng thái</label>

        <select name="trang_thai" required>

            <option value="">
                -- Chọn trạng thái --
            </option>

            <option value="Trống">
                Trống
            </option>

            <option value="Đang sử dụng">
                Đang sử dụng
            </option>

            <option value="Bảo trì">
                Bảo trì
            </option>

        </select>


        <button type="submit">
            Thêm phòng
        </button>

    </form>


    <!-- =================================================
         DANH SÁCH PHÒNG
    ================================================== -->

    <h2>Danh sách phòng thực hành</h2>

    <table>

        <tr>

            <th>STT</th>

            <th>Mã phòng</th>

            <th>Tên phòng</th>

            <th>Sức chứa</th>

            <th>Trạng thái</th>

            <th>Khả năng đặt</th>

        </tr>


        <?php

        // =================================================
        // 4. VÒNG LẶP DUYỆT MẢNG
        // =================================================

        $stt = 1;

        foreach ($rooms as $room) {

        ?>

            <tr>

                <td>
                    <?php echo $stt; ?>
                </td>

                <td>
                    <?php echo $room["ma_phong"]; ?>
                </td>

                <td>
                    <?php echo $room["ten_phong"]; ?>
                </td>

                <td>
                    <?php echo $room["suc_chua"]; ?> người
                </td>

                <td>
                    <?php echo $room["trang_thai"]; ?>
                </td>

                <td>

                    <?php
                    // Gọi hàm tự định nghĩa
                    echo kiemTraPhong($room["trang_thai"]);
                    ?>

                </td>

            </tr>

        <?php

            $stt++;

        }

        ?>

    </table>

</div>


</body>

</html>