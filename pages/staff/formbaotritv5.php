<?php
session_start();

$_SESSION["thietbi"] = [
    [
        "ten" => "Máy tính Dell",
        "loai" => "Máy tính",
        "phong" => "Phòng A101",
        "trangthai" => "Hỏng",
        "ngay_bat_dau" => "",
        "ngay_hoan_thanh" => "",
        "noi_dung_baotri" => "",
        "lich_su" => []
    ],
    [
        "ten" => "Máy chiếu Epson",
        "loai" => "Máy chiếu",
        "phong" => "Phòng A102",
        "trangthai" => "Đang bảo trì",
        "ngay_bat_dau" => "2026-08-15",
        "ngay_hoan_thanh" => "2026-08-20",
        "noi_dung_baotri" => "Thay bóng đèn máy chiếu",
        "lich_su" => [
            [
                "ngay" => "2026-08-15",
                "noi_dung" => "Thay bóng đèn máy chiếu",
                "trangthai" => "Đang bảo trì"
            ]
        ]
    ],
    [
        "ten" => "Máy in Canon",
        "loai" => "Máy in",
        "phong" => "Phòng A103",
        "trangthai" => "Hoạt động",
        "ngay_bat_dau" => "",
        "ngay_hoan_thanh" => "",
        "noi_dung_baotri" => "",
        "lich_su" => []
    ]
];

if (isset($_POST["batdau_baotri"])) {

    $i = (int)$_POST["index"];

    if (isset($_SESSION["thietbi"][$i])) {

        $ngay_bat_dau = $_POST["ngay_bat_dau"];
        $ngay_hoan_thanh = $_POST["ngay_hoan_thanh"];
        $noi_dung = trim($_POST["noi_dung_baotri"]);

        $_SESSION["thietbi"][$i]["trangthai"] = "Đang bảo trì";
        $_SESSION["thietbi"][$i]["ngay_bat_dau"] = $ngay_bat_dau;
        $_SESSION["thietbi"][$i]["ngay_hoan_thanh"] = $ngay_hoan_thanh;
        $_SESSION["thietbi"][$i]["noi_dung_baotri"] = $noi_dung;

        if (!isset($_SESSION["thietbi"][$i]["lich_su"])) {
            $_SESSION["thietbi"][$i]["lich_su"] = [];
        }

        $_SESSION["thietbi"][$i]["lich_su"][] = [
            "ngay" => $ngay_bat_dau,
            "noi_dung" => $noi_dung,
            "trangthai" => "Đang bảo trì"
        ];
    }
}

if (isset($_POST["capnhat"])) {

    $i = (int)$_POST["index"];

    if (isset($_SESSION["thietbi"][$i])) {

        $trangthai_moi = $_POST["trangthai_moi"];

        $_SESSION["thietbi"][$i]["trangthai"] = $trangthai_moi;

        if ($trangthai_moi == "Hoạt động") {
            $_SESSION["thietbi"][$i]["ngay_bat_dau"] = "";
            $_SESSION["thietbi"][$i]["ngay_hoan_thanh"] = "";
            $_SESSION["thietbi"][$i]["noi_dung_baotri"] = "";
        }
    }
}

$tong = count($_SESSION["thietbi"]);

$hoatdong = 0;
$hong = 0;
$baotri = 0;

foreach ($_SESSION["thietbi"] as $tb) {

    if ($tb["trangthai"] == "Hoạt động") {
        $hoatdong++;
    } elseif ($tb["trangthai"] == "Hỏng") {
        $hong++;
    } elseif ($tb["trangthai"] == "Đang bảo trì") {
        $baotri++;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Phòng bảo trì</title>

<style>

* {
    box-sizing: border-box;
}

:root {
    --hnmu-blue: #003399;
    --hnmu-blue-dark: #002266;
}

body {
    background-color: #f4f7f9;
    color: #333;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    padding: 40px 0;
}

.container {
    inline-size: 1100px;
    max-inline-size: 96%;
    margin: auto;
    padding: 30px;
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

h2 {
    color: var(--hnmu-blue);
    text-align: center;
    margin-block-end: 10px;
}

.mo-ta {
    text-align: center;
    color: #666;
    margin-block-end: 30px;
}

.thong-ke {
    display: flex;
    gap: 15px;
    margin-block-end: 30px;
}

.box {
    flex: 1;
    padding: 15px;
    text-align: center;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.box strong {
    display: block;
    font-size: 25px;
    margin-block-start: 5px;
}

table {
    inline-size: 100%;
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #dee2e6;
    padding: 10px;
    text-align: center;
    vertical-align: middle;
}

thead {
    background-color: var(--hnmu-blue);
    color: white;
}

tbody tr {
    background-color: #ffffff;
}

tbody tr:hover {
    background-color: #ffffff;
}

.status {
    font-weight: bold;
}

.hong {
    color: #dc3545;
}

.baotri {
    color: #fd7e14;
}

.hoatdong {
    color: #198754;
}

.form-baotri {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-baotri input,
.form-baotri select,
.form-baotri textarea {
    padding: 7px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-family: inherit;
}

.form-baotri textarea {
    min-block-size: 60px;
    resize: vertical;
}

.btn {
    inline-size: 100%;
    padding: 8px 10px;
    border: none;
    border-radius: 5px;
    background-color: var(--hnmu-blue);
    color: white;
    cursor: pointer;
    font-family: inherit;
    font-size: 14px;
    transition: background-color 0.2s ease;
}

.btn:hover {
    background-color: var(--hnmu-blue-dark);
}

.btn-disabled {
    color: #777;
    display: inline-block;
    padding: 8px;
}

.back {
    display: inline-block;
    margin-block-start: 25px;
    color: var(--hnmu-blue);
    text-decoration: none;
    font-weight: bold;
}

.back:hover {
    color: var(--hnmu-blue-dark);
    text-decoration: underline;
}

</style>

</head>

<body>

<div class="container">

<h2>PHÒNG BẢO TRÌ THIẾT BỊ</h2>

<p class="mo-ta">
    Quản lý bảo trì và cập nhật trạng thái thiết bị
</p>

<div class="thong-ke">

    <div class="box">
        Tổng thiết bị
        <strong><?= $tong ?></strong>
    </div>

    <div class="box">
        Hoạt động
        <strong><?= $hoatdong ?></strong>
    </div>

    <div class="box">
        Hỏng
        <strong><?= $hong ?></strong>
    </div>

    <div class="box">
        Đang bảo trì
        <strong><?= $baotri ?></strong>
    </div>

</div>

<table>

<thead>

<tr>
    <th>STT</th>
    <th>Tên thiết bị</th>
    <th>Loại</th>
    <th>Phòng</th>
    <th>Trạng thái</th>
    <th>Thời gian bảo trì</th>
    <th>Thao tác</th>
</tr>

</thead>

<tbody>

<?php foreach ($_SESSION["thietbi"] as $i => $tb): ?>

<tr>

<td>
    <?= $i + 1 ?>
</td>

<td>
    <?= htmlspecialchars($tb["ten"]) ?>
</td>

<td>
    <?= htmlspecialchars($tb["loai"]) ?>
</td>

<td>
    <?= htmlspecialchars($tb["phong"]) ?>
</td>

<td class="status">

<?php if ($tb["trangthai"] == "Hỏng"): ?>

    <span class="hong">Hỏng</span>

<?php elseif ($tb["trangthai"] == "Đang bảo trì"): ?>

    <span class="baotri">Đang bảo trì</span>

<?php else: ?>

    <span class="hoatdong">Hoạt động</span>

<?php endif; ?>

</td>

<td>

<?php if ($tb["trangthai"] == "Đang bảo trì"): ?>

    Bắt đầu:
    <?= htmlspecialchars($tb["ngay_bat_dau"]) ?>

    <br>

    Hoàn thành:
    <?= htmlspecialchars($tb["ngay_hoan_thanh"]) ?>

<?php else: ?>

    -

<?php endif; ?>

</td>

<td>

<?php if ($tb["trangthai"] == "Hỏng"): ?>

<form method="post" class="form-baotri">

    <input
        type="hidden"
        name="index"
        value="<?= $i ?>"
    >

    <label>Ngày bắt đầu</label>

    <input
        type="date"
        name="ngay_bat_dau"
        required
    >

    <label>Ngày hoàn thành</label>

    <input
        type="date"
        name="ngay_hoan_thanh"
        required
    >

    <textarea
        name="noi_dung_baotri"
        placeholder="Nội dung bảo trì"
        required
    ></textarea>

    <button
        type="submit"
        name="batdau_baotri"
        class="btn"
    >
        Bảo trì
    </button>

</form>

<?php elseif ($tb["trangthai"] == "Đang bảo trì"): ?>

<form method="post" class="form-baotri">

    <input
        type="hidden"
        name="index"
        value="<?= $i ?>"
    >

    <select name="trangthai_moi">

        <option value="Đang bảo trì">
            Đang bảo trì
        </option>

        <option value="Hoạt động">
            Hoạt động
        </option>

        <option value="Hỏng">
            Hỏng
        </option>

    </select>

    <button
        type="submit"
        name="capnhat"
        class="btn"
    >
        Cập nhật trạng thái
    </button>

</form>

<?php else: ?>

<span class="btn-disabled">
    Không cần bảo trì
</span>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<a href="thietbi.php" class="back">
    ← Quay lại quản lý thiết bị
</a>

</div>

</body>

</html>