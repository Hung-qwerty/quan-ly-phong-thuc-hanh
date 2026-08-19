<?php

session_start();

$thietbi_mau = [
    [
        "id" => 101,
        "ten" => "Máy chiếu Epson",
        "loai" => "Máy chiếu",
        "phong" => "Phòng A101",
        "trangthai" => "Đang bảo trì",
        "ngay_bat_dau" => "2026-08-15",
        "ngay_hoan_thanh" => "2026-08-20",
        "noi_dung_baotri" => "Thay bóng đèn máy chiếu"
    ],

    [
        "id" => 102,
        "ten" => "Máy tính Dell",
        "loai" => "Máy tính",
        "phong" => "Phòng A102",
        "trangthai" => "Hỏng",
        "ngay_bat_dau" => "",
        "ngay_hoan_thanh" => "",
        "noi_dung_baotri" => ""
    ],

    [
        "id" => 103,
        "ten" => "Máy in Canon",
        "loai" => "Máy in",
        "phong" => "Phòng A103",
        "trangthai" => "Hoạt động",
        "ngay_bat_dau" => "",
        "ngay_hoan_thanh" => "",
        "noi_dung_baotri" => ""
    ]
];


if (isset($_POST["reset"])) {

    $_SESSION["thietbi"] = $thietbi_mau;

    header("Location: " . $_SERVER["PHP_SELF"]);

    exit;
}


if (!isset($_SESSION["thietbi"])) {

    $_SESSION["thietbi"] = $thietbi_mau;
}

$thietbi = &$_SESSION["thietbi"];

$thongbao = "";
$loi = "";


function timThietBi($id, $thietbi)
{
    foreach ($thietbi as $index => $tb) {

        if ((int)$tb["id"] === (int)$id) {

            return $index;
        }
    }

    return -1;
}


if (isset($_POST["batdau_baotri"])) {

    $id = (int)($_POST["id"] ?? 0);

    $index = timThietBi($id, $thietbi);

    if ($index === -1) {

        $loi = "Không tìm thấy thiết bị.";

    } else {

        $ngay_bat_dau =
            trim($_POST["ngay_bat_dau"] ?? "");

        $ngay_hoan_thanh =
            trim($_POST["ngay_hoan_thanh"] ?? "");

        $noi_dung =
            trim($_POST["noi_dung_baotri"] ?? "");


        if ($thietbi[$index]["trangthai"] !== "Hỏng") {

            $loi =
                "Chỉ thiết bị đang Hỏng mới được bắt đầu bảo trì.";

        } elseif ($ngay_bat_dau === "") {

            $loi =
                "Vui lòng nhập ngày bắt đầu.";

        } elseif ($ngay_hoan_thanh === "") {

            $loi =
                "Vui lòng nhập ngày hoàn thành.";

        } elseif ($noi_dung === "") {

            $loi =
                "Vui lòng nhập nội dung bảo trì.";

        } elseif ($ngay_hoan_thanh < $ngay_bat_dau) {

            $loi =
                "Ngày hoàn thành không được nhỏ hơn ngày bắt đầu.";

        } else {

            $thietbi[$index]["trangthai"] =
                "Đang bảo trì";

            $thietbi[$index]["ngay_bat_dau"] =
                $ngay_bat_dau;

            $thietbi[$index]["ngay_hoan_thanh"] =
                $ngay_hoan_thanh;

            $thietbi[$index]["noi_dung_baotri"] =
                $noi_dung;


            $thongbao =
                "Thiết bị " .
                htmlspecialchars($thietbi[$index]["ten"]) .
                " đã bắt đầu bảo trì.";
        }
    }
}


if (isset($_POST["capnhat"])) {

    $id =
        (int)($_POST["id"] ?? 0);

    $trangthai_moi =
        trim($_POST["trangthai_moi"] ?? "");

    $index =
        timThietBi($id, $thietbi);


    if ($index === -1) {

        $loi =
            "Không tìm thấy thiết bị.";

    } elseif (
        $trangthai_moi !== "Hoạt động" &&
        $trangthai_moi !== "Hỏng" &&
        $trangthai_moi !== "Đang bảo trì"
    ) {

        $loi =
            "Trạng thái không hợp lệ.";

    } elseif (
        $thietbi[$index]["trangthai"] !== "Đang bảo trì"
    ) {

        $loi =
            "Chỉ thiết bị đang bảo trì mới được cập nhật.";

    } else {


        $thietbi[$index]["trangthai"] =
            $trangthai_moi;


        if ($trangthai_moi === "Hoạt động") {

            $thietbi[$index]["ngay_bat_dau"] = "";

            $thietbi[$index]["ngay_hoan_thanh"] = "";

            $thietbi[$index]["noi_dung_baotri"] = "";


            $thongbao =
                "Thiết bị " .
                htmlspecialchars($thietbi[$index]["ten"]) .
                " đã chuyển sang Hoạt động.";

        } elseif ($trangthai_moi === "Hỏng") {

            $thietbi[$index]["ngay_bat_dau"] = "";

            $thietbi[$index]["ngay_hoan_thanh"] = "";

            $thietbi[$index]["noi_dung_baotri"] = "";


            $thongbao =
                "Thiết bị " .
                htmlspecialchars($thietbi[$index]["ten"]) .
                " đã chuyển sang Hỏng.";

        } else {

            $thongbao =
                "Thiết bị " .
                htmlspecialchars($thietbi[$index]["ten"]) .
                " vẫn đang bảo trì.";
        }
    }
}


$tong = count($thietbi);

$hoatdong = 0;
$hong = 0;
$baotri = 0;


foreach ($thietbi as $tb) {

    if ($tb["trangthai"] === "Hoạt động") {

        $hoatdong++;

    } elseif ($tb["trangthai"] === "Hỏng") {

        $hong++;

    } elseif ($tb["trangthai"] === "Đang bảo trì") {

        $baotri++;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

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

.thong-bao {
    padding: 12px;
    margin-block-end: 20px;
    border-radius: 6px;
    background-color: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
    text-align: center;
}

.thong-bao-loi {
    padding: 12px;
    margin-block-end: 20px;
    border-radius: 6px;
    background-color: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
    text-align: center;
}

.table-wrap {
    overflow-x: auto;
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
    inline-size: 100%;
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
}

.btn:hover {
    background-color: var(--hnmu-blue-dark);
}

.btn-reset {
    inline-size: auto;
    padding: 8px 15px;
    margin-block-end: 20px;
    background-color: #6c757d;
}

.btn-reset:hover {
    background-color: #495057;
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


<?php if ($thongbao !== ""): ?>

<div class="thong-bao">
    <?= $thongbao ?>
</div>

<?php endif; ?>


<?php if ($loi !== ""): ?>

<div class="thong-bao-loi">
    <?= htmlspecialchars($loi) ?>
</div>

<?php endif; ?>


<!-- RESET -->

<form method="post">

<button
    type="submit"
    name="reset"
    class="btn btn-reset"
>
    Reset dữ liệu demo
</button>

</form>


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



<div class="table-wrap">

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

<?php foreach ($thietbi as $index => $tb): ?>

<tr>

<td>
    <?= $index + 1 ?>
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

<?php if ($tb["trangthai"] === "Hỏng"): ?>

<span class="hong">
    Hỏng
</span>

<?php elseif ($tb["trangthai"] === "Đang bảo trì"): ?>

<span class="baotri">
    Đang bảo trì
</span>

<?php else: ?>

<span class="hoatdong">
    Hoạt động
</span>

<?php endif; ?>

</td>


<td>

<?php if ($tb["trangthai"] === "Đang bảo trì"): ?>

Bắt đầu:
<?= htmlspecialchars($tb["ngay_bat_dau"]) ?>

<br>

Hoàn thành:
<?= htmlspecialchars($tb["ngay_hoan_thanh"]) ?>

<br>

<?= htmlspecialchars($tb["noi_dung_baotri"]) ?>

<?php else: ?>

-

<?php endif; ?>

</td>


<td>


<?php if ($tb["trangthai"] === "Hỏng"): ?>

<form method="post"
      class="form-baotri">

<input
    type="hidden"
    name="id"
    value="<?= $tb["id"] ?>"
>

<label>
    Ngày bắt đầu
</label>

<input
    type="date"
    name="ngay_bat_dau"
    required
>

<label>
    Ngày hoàn thành
</label>

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


<?php elseif ($tb["trangthai"] === "Đang bảo trì"): ?>

<form method="post"
      class="form-baotri">

<input
    type="hidden"
    name="id"
    value="<?= $tb["id"] ?>"
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

</div>


<a href="thietbi.php"
   class="back">

    ← Quay lại quản lý thiết bị

</a>

</div>

</body>

</html>