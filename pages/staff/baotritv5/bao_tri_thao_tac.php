<?php

/*
|--------------------------------------------------------------------------
| THIẾT BỊ HỎNG
|--------------------------------------------------------------------------
*/

if (
    $deviceStatus === "Hỏng"
    || $deviceStatus === "broken"
):

?>

<form
    method="post"
    class="form-baotri"
>

    <input
        type="hidden"
        name="device_id"
        value="<?= (int)$tb["device_id"] ?>"
    >

    <textarea
        name="description"
        placeholder="Nội dung bảo trì..."
        required
    ></textarea>

    <button
        type="submit"
        name="batdau_baotri"
        class="btn"
    >
        Bắt đầu bảo trì
    </button>

</form>


<?php

/*
|--------------------------------------------------------------------------
| THIẾT BỊ ĐANG BẢO TRÌ
|--------------------------------------------------------------------------
*/

elseif (
    $deviceStatus === "Đang bảo trì"
    || $deviceStatus === "maintenance"
):

?>


<?php if ($maintenanceId > 0): ?>

<form
    method="post"
    class="form-baotri"
>

    <input
        type="hidden"
        name="maintenance_id"
        value="<?= $maintenanceId ?>"
    >

    <input
        type="hidden"
        name="device_id"
        value="<?= (int)$tb["device_id"] ?>"
    >


    <select name="status">

        <option value="Đang bảo trì">
            Đang bảo trì
        </option>

        <option value="Hoàn thành">
            Hoàn thành
        </option>

    </select>


    <textarea
        name="result"
        placeholder="Nhập kết quả bảo trì..."
        required
    ></textarea>


    <button
        type="submit"
        name="capnhat"
        class="btn"
    >
        Cập nhật
    </button>

</form>


<?php else: ?>

<span class="muted">
    Thiết bị đang bảo trì nhưng chưa có phiếu.
</span>

<?php endif; ?>


<?php

/*
|--------------------------------------------------------------------------
| THIẾT BỊ HOẠT ĐỘNG
|--------------------------------------------------------------------------
*/

else:

?>

<span class="muted">
    Không cần bảo trì
</span>

<?php endif; ?>