<?php

function h($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}


function trangThaiClass(string $status): string
{
    if (
        in_array(
            $status,
            ["Hỏng", "broken"],
            true
        )
    ) {
        return "hong";
    }


    if (
        in_array(
            $status,
            ["Đang bảo trì", "maintenance"],
            true
        )
    ) {
        return "baotri";
    }


    return "hoatdong";
}


function trangThaiText(string $status): string
{
    if (
        in_array(
            $status,
            ["Hỏng", "broken"],
            true
        )
    ) {
        return "Hỏng";
    }


    if (
        in_array(
            $status,
            ["Đang bảo trì", "maintenance"],
            true
        )
    ) {
        return "Đang bảo trì";
    }


    if (
        in_array(
            $status,
            ["Hoạt động", "working", "active"],
            true
        )
    ) {
        return "Hoạt động";
    }


    return $status !== ""
        ? $status
        : "Chưa xác định";
}