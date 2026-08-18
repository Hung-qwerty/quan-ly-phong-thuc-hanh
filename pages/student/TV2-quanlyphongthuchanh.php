<?php

function e($v){
    return htmlspecialchars($v,ENT_QUOTES,'UTF-8');
}

$rooms=[
    ["id"=>"P101","name"=>"Phòng máy 101","capacity"=>40,"status"=>"Trống"],
    ["id"=>"P102","name"=>"Phòng máy 102","capacity"=>35,"status"=>"Đang sử dụng"],
    ["id"=>"P103","name"=>"Phòng máy 103","capacity"=>45,"status"=>"Bảo trì"],
    ["id"=>"P104","name"=>"Phòng máy 104","capacity"=>40,"status"=>"Trống"]
];

$page=$_GET["page"]??"home";

$data=[
    "name"=>"",
    "email"=>"",
    "room"=>"",
    "date"=>"",
    "start"=>"",
    "end"=>"",
    "purpose"=>""
];

$errors=[];
$success="";

if($_SERVER["REQUEST_METHOD"]==="POST"){

    $page=$_POST["page"]??"booking";

    foreach($data as $key=>$value){
        $data[$key]=trim($_POST[$key]??"");
    }

    if($data["name"]==="")
        $errors["name"]="Vui lòng nhập họ và tên.";

    if($data["email"]==="")
        $errors["email"]="Vui lòng nhập email.";
    elseif(!filter_var($data["email"],FILTER_VALIDATE_EMAIL))
        $errors["email"]="Email không đúng định dạng.";

    $validRooms=[];
    foreach($rooms as $room){
        if($room["status"]==="Trống")
            $validRooms[]=$room["id"];
    }

    if($data["room"]==="")
        $errors["room"]="Vui lòng chọn phòng.";
    elseif(!in_array($data["room"],$validRooms))
        $errors["room"]="Phòng không hợp lệ.";

    if($data["date"]==="")
        $errors["date"]="Vui lòng chọn ngày.";

    if($data["start"]==="")
        $errors["start"]="Vui lòng chọn giờ bắt đầu.";

    if($data["end"]==="")
        $errors["end"]="Vui lòng chọn giờ kết thúc.";
    elseif($data["start"]!=="" && $data["end"]<=$data["start"])
        $errors["end"]="Giờ kết thúc phải lớn hơn giờ bắt đầu.";

    if($data["purpose"]==="")
        $errors["purpose"]="Vui lòng nhập mục đích.";
    elseif(mb_strlen($data["purpose"])<10)
        $errors["purpose"]="Mục đích phải có ít nhất 10 ký tự.";

    if(empty($errors))
        $success="Gửi yêu cầu đặt phòng thành công!";
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1">

<title>Lab Management</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f7f9;
    color:#333;
}

.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:220px;
    height:100vh;
    background:white;
    border-right:1px solid #ddd;
    padding:25px 15px;
}

.logo{
    font-size:18px;
    font-weight:bold;
    color:#003399;
    margin-bottom:35px;
}

.menu a{
    display:block;
    padding:13px;
    margin-bottom:5px;
    text-decoration:none;
    color:#444;
    border-radius:6px;
}

.menu a:hover,
.menu .active{
    background:#eef4ff;
    color:#003399;
}

.main{
    margin-left:220px;
}

.header{
    height:60px;
    background:white;
    border-bottom:1px solid #ddd;
    padding:0 30px;
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.content{
    padding:35px;
}

h1,h2,h3{
    color:#003399;
}

.card{
    background:white;
    border:1px solid #ddd;
    border-radius:8px;
    padding:22px;
}

.cards{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin-top:25px;
}

.card p{
    color:#777;
    line-height:1.5;
}

.card a{
    color:#003399;
    text-decoration:none;
    font-weight:bold;
}

.rooms{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

.status{
    display:inline-block;
    padding:5px 10px;
    border-radius:15px;
    font-size:12px;
}

.available{
    background:#e5f7eb;
    color:#207a45;
}

.busy{
    background:#ffe5e5;
    color:#d32f2f;
}

.maintenance{
    background:#fff3cd;
    color:#856404;
}

.form{
    max-width:700px;
    background:white;
    border:1px solid #ddd;
    border-radius:8px;
    padding:25px;
}

.group{
    margin-bottom:17px;
}

label{
    display:block;
    font-weight:bold;
    margin-bottom:6px;
}

input,
select,
textarea{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:5px;
    font-family:Arial;
}

textarea{
    min-height:100px;
    resize:vertical;
}

.error-input{
    border-color:#d32f2f;
}

.error{
    color:#d32f2f;
    font-size:13px;
    margin-top:5px;
}

.success{
    max-width:700px;
    background:#e6f7ed;
    color:#207a45;
    padding:12px;
    border-radius:5px;
    margin-bottom:15px;
}

button{
    background:#003399;
    color:white;
    border:0;
    padding:11px 18px;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#002266;
}

@media(max-width:800px){

    .sidebar{
        width:180px;
    }

    .main{
        margin-left:180px;
    }

    .cards,
    .rooms{
        grid-template-columns:1fr;
    }

}

</style>

</head>

<body>

<aside class="sidebar">

<div class="logo">
🏫 LAB MANAGEMENT
</div>

<nav class="menu">

<a
href="?page=home"
class="<?= $page==="home"?"active":"" ?>"
>
🏠 Trang chủ
</a>

<a
href="?page=rooms"
class="<?= $page==="rooms"?"active":"" ?>"
>
🏫 Phòng thực hành
</a>

<a
href="?page=booking"
class="<?= $page==="booking"?"active":"" ?>"
>
📅 Đặt phòng
</a>

<a
href="?page=report"
class="<?= $page==="report"?"active":"" ?>"
>
🔧 Báo hỏng
</a>

</nav>

</aside>

<main class="main">

<header class="header">

<span>Student Portal</span>

<strong>👤 Nguyễn Văn A</strong>

</header>

<div class="content">

<?php if($page==="home"): ?>

<h1>Xin chào, Nguyễn Văn A! 👋</h1>

<p>
Chào mừng bạn đến với hệ thống quản lý phòng thực hành.
</p>

<div class="cards">

<div class="card">

<h3>🏫 Phòng thực hành</h3>

<p>
Xem danh sách phòng, sức chứa và trạng thái phòng.
</p>

<a href="?page=rooms">
Xem phòng →
</a>

</div>

<div class="card">

<h3>📅 Đặt phòng</h3>

<p>
Gửi yêu cầu sử dụng phòng thực hành.
</p>

<a href="?page=booking">
Đặt phòng →
</a>

</div>

<div class="card">

<h3>🔧 Báo hỏng</h3>

<p>
Gửi thông báo khi phát hiện thiết bị gặp sự cố.
</p>

<a href="?page=report">
Báo hỏng →
</a>

</div>

</div>

<?php elseif($page==="rooms"): ?>

<h1>Phòng thực hành</h1>

<p>
Danh sách các phòng thực hành hiện có.
</p>

<div class="rooms">

<?php foreach($rooms as $room): ?>

<?php

$class=
$room["status"]==="Trống"
?"available"
:($room["status"]==="Đang sử dụng"
?"busy"
:"maintenance");

?>

<div class="card">

<h3><?=e($room["id"])?></h3>

<p><?=e($room["name"])?></p>

<p>
Sức chứa:
<strong><?=e($room["capacity"])?> người</strong>
</p>

<span class="status <?=$class?>">
<?=e($room["status"])?>
</span>

</div>

<?php endforeach; ?>

</div>

<?php elseif($page==="booking"): ?>

<h1>Đặt phòng</h1>

<?php if($success): ?>

<div class="success">
<?=e($success)?>
</div>

<?php endif; ?>

<div class="form">

<form method="POST">

<input
type="hidden"
name="page"
value="booking"
>

<div class="group">

<label>Họ và tên *</label>

<input
type="text"
name="name"
value="<?=e($data["name"])?>"
class="<?=$errors["name"]??""?'error-input':''?>"
>

<?php if(isset($errors["name"])): ?>

<div class="error">
<?=e($errors["name"])?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Email *</label>

<input
type="text"
name="email"
value="<?=e($data["email"])?>"
class="<?=isset($errors["email"])?"error-input":""?>"
>

<?php if(isset($errors["email"])): ?>

<div class="error">
<?=e($errors["email"])?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Phòng *</label>

<select
name="room"
class="<?=isset($errors["room"])?"error-input":""?>"
>

<option value="">
-- Chọn phòng --
</option>

<?php foreach($rooms as $room): ?>

<?php if($room["status"]==="Trống"): ?>

<option
value="<?=e($room["id"])?>"
<?=$data["room"]===$room["id"]?"selected":""?>
>

<?=e($room["id"])?> -
<?=e($room["name"])?>

</option>

<?php endif; ?>

<?php endforeach; ?>

</select>

<?php if(isset($errors["room"])): ?>

<div class="error">
<?=e($errors["room"])?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Ngày đặt phòng *</label>

<input
type="date"
name="date"
value="<?=e($data["date"])?>"
class="<?=isset($errors["date"])?"error-input":""?>"
>

<?php if(isset($errors["date"])): ?>

<div class="error">
<?=e($errors["date"])?>
</div>

<?php endif; ?>

</div>

<div
style="
display:grid;
grid-template-columns:1fr 1fr;
gap:15px
"
>

<div class="group">

<label>Bắt đầu *</label>

<input
type="time"
name="start"
value="<?=e($data["start"])?>"
class="<?=isset($errors["start"])?"error-input":""?>"
>

<?php if(isset($errors["start"])): ?>

<div class="error">
<?=e($errors["start"])?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Kết thúc *</label>

<input
type="time"
name="end"
value="<?=e($data["end"])?>"
class="<?=isset($errors["end"])?"error-input":""?>"
>

<?php if(isset($errors["end"])): ?>

<div class="error">
<?=e($errors["end"])?>
</div>

<?php endif; ?>

</div>

</div>

<div class="group">

<label>Mục đích sử dụng *</label>

<textarea
name="purpose"
class="<?=isset($errors["purpose"])?"error-input":""?>"
><?=e($data["purpose"])?></textarea>

<?php if(isset($errors["purpose"])): ?>

<div class="error">
<?=e($errors["purpose"])?>
</div>

<?php endif; ?>

</div>

<button type="submit">
Gửi yêu cầu
</button>

</form>

</div>

<?php elseif($page==="report"): ?>

<h1>Báo hỏng thiết bị</h1>

<div class="form">

<div class="group">

<label>Tên thiết bị</label>

<input
type="text"
placeholder="Nhập tên thiết bị"
>

</div>

<div class="group">

<label>Phòng</label>

<select>

<option>-- Chọn phòng --</option>

<option>P101</option>

<option>P102</option>

<option>P103</option>

<option>P104</option>

</select>

</div>

<div class="group">

<label>Mô tả sự cố</label>

<textarea
placeholder="Nhập mô tả sự cố"
></textarea>

</div>

<button
type="button"
onclick="alert('Đã gửi báo hỏng!')"
>
Gửi báo hỏng
</button>

</div>

<?php endif; ?>

</div>

</main>

</body>
</html>