<?php
namespace App\Core;

use PDO;

class Database {
    public static function connection(): PDO {
        $dsn = 'mysql:host=localhost;dbname=quan_ly_phong_thuc_hanh;charset=utf8mb4';
        return new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}