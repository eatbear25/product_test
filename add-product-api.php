<?php
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json');

$output = [
  'success' => false,
  'postData' => $_POST,
  'error' => '',
  'errorFields' => []
];

// TODO: 欄位檢查
# 欄位的資料檢查
// $name = trim($_POST['name'] ?? '');
// $email = mb_strtolower(trim($_POST['email'] ?? '')); # 去掉頭尾空白, 轉成小寫字母
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

$isPass = true;

// if (empty($name)) {
//   $isPass = false;
//   $output['errorFields']['name'] = '姓名為必填欄位';
// } elseif (mb_strlen($name) < 2) {
//   $isPass = false;
//   $output['errorFields']['name'] = '請填寫正確的姓名';
// }

// if (empty($email)) {
//   $isPass = false;
//   $output['errorFields']['email'] = 'Email 為必填欄位';
// } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
//   $isPass = false;
//   $output['errorFields']['email'] = '請填寫正確的 Email 格式';
// }

// if (! $isPass) {
//   echo json_encode($output, JSON_UNESCAPED_UNICODE);
//   exit;
// }

$sql = "INSERT INTO `product` (
    `name`, `content`, `category_id`, `stock`, `price`, `status`
    ) VALUES (
      ?,
      ?,
      ?,
      ?,
      ?,
      -- ?,
      ?
    )";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['name'],
    $_POST['content'],
    $_POST['category'],
    $_POST['stock'],
    $_POST['price'],
    $status
    // $_POST['photo']
  ]);

  # $stmt->rowCount() 影響的列數, 新增的話就是新增幾筆
  $output['success'] = !! $stmt->rowCount();
  $output['id'] = $pdo->lastInsertId(); # 最近新增資料的 PK
} catch (PDOException $ex) {
  $output['error'] = $ex->getMessage();
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);