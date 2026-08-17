<?php

$devices = [
    [
        "id" => 1,
        "name" => "Máy tính DELL",
        "type" => "Máy tính",
        "room" => "A3 - 210"
    ],
    [
        "id" => 2,
        "name" => "Bàn phím Fulhen",
        "type" => "Bàn phím",
        "room" => "A3 - 211"
    ],
    [
        "id" => 3,
        "name" => "TV LG",
        "type" => "TV",
        "room" => "A3 - 210"
    ]
];

function kiemTraThietBi($name, $type, $room)
{
    if ($name == "" || $type == "" || $room == "") {
        return false;
    }

    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $type = trim($_POST["type"] ?? "");
    $room = trim($_POST["room"] ?? "");

    if (kiemTraThietBi($name, $type, $room)) {

        $device = [
            "id" => count($devices) + 1,
            "name" => $name,
            "type" => $type,
            "room" => $room
        ];

        echo json_encode([
            "success" => true,
            "message" => "Thêm thiết bị thành công!",
            "device" => $device
        ], JSON_UNESCAPED_UNICODE);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Vui lòng nhập đầy đủ thông tin!"
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Quản lý thiết bị</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f4f7f9;
    color: #333;
    font-family: "Segoe UI", Arial, sans-serif;
}

.container {
    width: 90%;
    max-width: 1050px;
    margin: 40px auto;
}

h2 {
    color: #003399;
    margin-bottom: 5px;
}

.subtitle {
    color: #777;
    margin-bottom: 25px;
}

.card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    margin-bottom: 20px;
}

.card-title {
    padding: 18px 20px;
    border-bottom: 1px solid #dee2e6;
}

.card-title h3 {
    margin: 0;
    color: #003399;
}

.form {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    outline: none;
    font-family: inherit;
}

input:focus {
    border-color: #003399;
    box-shadow: 0 0 0 3px rgba(0, 51, 153, .12);
}

button {
    padding: 10px 18px;
    border: 0;
    border-radius: 6px;
    background: #003399;
    color: white;
    cursor: pointer;
    font-family: inherit;
}

button:hover {
    background: #002266;
}

.message {
    display: none;
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    background: #e8f5ee;
    color: #198754;
    border: 1px solid #c8e6d3;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #003399;
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

tr:hover td {
    background: #f7f9fc;
}

@media (max-width: 700px) {

    .container {
        width: 94%;
    }

    table {
        font-size: 14px;
    }

}

</style>

</head>

<body>

<div class="container">

    <h2>QUẢN LÝ THIẾT BỊ</h2>


    <div id="message" class="message"></div>


    <div class="card">

        <div class="card-title">

            <h3>Thêm thiết bị</h3>

        </div>


        <form id="deviceForm" class="form">

            <div class="form-group">

                <label for="name">
                    Tên thiết bị
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Nhập tên thiết bị"
                    required>

            </div>


            <div class="form-group">

                <label for="type">
                    Loại thiết bị
                </label>

                <input
                    type="text"
                    id="type"
                    name="type"
                    placeholder="Nhập loại thiết bị"
                    required>

            </div>


            <div class="form-group">

                <label for="room">
                    Phòng
                </label>

                <input
                    type="text"
                    id="room"
                    name="room"
                    placeholder="Nhập phòng"
                    required>

            </div>


            <button type="submit">
                Thêm thiết bị
            </button>

        </form>

    </div>


    <div class="card">

        <div class="card-title">

            <h3>Danh sách thiết bị</h3>

        </div>


        <table>

            <thead>

                <tr>
                    <th>STT</th>
                    <th>Tên thiết bị</th>
                    <th>Loại thiết bị</th>
                    <th>Phòng</th>
                </tr>

            </thead>


            <tbody id="deviceList">

                <?php foreach ($devices as $i => $device): ?>

                    <tr>

                        <td>
                            <?php echo $i + 1; ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($device["name"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($device["type"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($device["room"]);
                            ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>


<script>

document.getElementById("deviceForm").addEventListener(
    "submit",
    function(event) {

        event.preventDefault();

        const form = new FormData(this);

        fetch("", {
            method: "POST",
            body: form
        })
        .then(response => response.json())
        .then(data => {

            const message = document.getElementById("message");

            message.textContent = data.message;
            message.style.display = "block";

            if (data.success) {

                const device = data.device;

                const list =
                    document.getElementById("deviceList");

                const row = document.createElement("tr");

                row.innerHTML =
                    "<td>" +
                    (list.rows.length + 1) +
                    "</td>" +

                    "<td>" +
                    escapeHtml(device.name) +
                    "</td>" +

                    "<td>" +
                    escapeHtml(device.type) +
                    "</td>" +

                    "<td>" +
                    escapeHtml(device.room) +
                    "</td>";

                list.appendChild(row);

                document.getElementById("deviceForm").reset();
            }
        });
    }
);

function escapeHtml(text) {

    const div = document.createElement("div");

    div.textContent = text;

    return div.innerHTML;
}

</script>

</body>

</html>