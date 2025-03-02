<?php

require __DIR__ . "/config.php";

$dsn = sprintf('mysql:host=%s;dbname=%s;port=%s;charset=utf8mb4', DB_HOST, DB_NAME, DB_PORT);

$pdo_options = [
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];


$pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);

# 啟用 Session 功能
// if (! isset($_SESSION)) {
//   session_start();
// }