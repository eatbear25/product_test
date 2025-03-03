<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json');

$output = [
  'success' => false,
  'postData' => $_POST,
  'error' => '',
  'errorFields' => []
];



# 欄位的資料檢查
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = trim($_POST['name'] ?? '');
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

$isPass = true;

// TODO: 後台欄位檢查
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

// * 驗證失敗的話就回傳 JSON 資料
if (! $isPass) {
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// * 編輯功能區
$sql = "UPDATE `product` SET 
    `name`=?,
    `content`=?,
    `category_id`=?,
    `stock`=?,
    `price`=?,
    `status`=?
    WHERE `id`=? ";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $_POST['name'],
    $_POST['content'],
    $_POST['category'],
    $_POST['stock'],
    $_POST['price'],
    $status,
    $id
  ]);

  # $stmt->rowCount() 影響的列數, 如果修改的資料和原本資料一樣, 會拿到 false
  $output['success'] = !! $stmt->rowCount();
} catch (PDOException $ex) {
  $output['error'] = $ex->getMessage();
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
