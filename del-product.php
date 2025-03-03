<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限

require __DIR__ . "/parts/db-connect.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $sql = " DELETE FROM product WHERE id=$id ";
  $pdo->query($sql);
}

$come_from = 'product-list.php';
if (! empty($_SERVER['HTTP_REFERER'])) {
  $come_from = $_SERVER['HTTP_REFERER'];
}

header("Location: $come_from");
