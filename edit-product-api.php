<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";

header('Content-Type: application/json');

$output = [
  'success' => false,
  'postData' => $_POST,
  'error' => '',
  'file' => '', # 儲存的檔名
  'errorFields' => []
];

// * 上傳圖片
$dir = __DIR__ . '/images/';

# 允許的圖片類型
$extMap = [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/webp' => '.webp',
];


# 欄位的資料檢查
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = trim($_POST['name'] ?? '');
$stock = intval($_POST['stock']);
$category = intval($_POST['category']);
$price = intval($_POST['price']);
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
$photo = '';

// * 圖片欄位驗證
$image_name = null; // 預設圖片欄位為 null
if (!empty($_FILES['photo']['name'])) {
  if (!is_string($_FILES['photo']['name']) || $_FILES['photo']['error'] != 0) {
    $isPass = false;
    $output['errorFields']['photo'] = '圖片上傳失敗';
  } elseif (empty($extMap[$_FILES['photo']['type']])) {
    $isPass = false;
    $output['errorFields']['photo'] = '不支援的圖片格式';
  } else {
    // * 產生唯一檔名
    $ext = $extMap[$_FILES['photo']['type']];
    $image_name = md5($_FILES['photo']['name'] . uniqid()) . $ext;
    $output['file'] = $image_name; // 存入 JSON 回應

    // * 搬運圖片到指定資料夾
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $image_name)) {
      $isPass = false;
      $output['errorFields']['photo'] = '圖片儲存失敗';
    }
  }
}

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
    `status`=?,
    `image`=?
    WHERE `id`=? ";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $name,
    $_POST['content'],
    $category,
    $stock,
    $price,
    $status,
    $image_name ?: $_POST['original_photo'],  // 如果沒有新圖片，保留原來的圖片
    $id
  ]);

  # $stmt->rowCount() 影響的列數, 如果修改的資料和原本資料一樣, 會拿到 false
  $output['success'] = !! $stmt->rowCount();
} catch (PDOException $ex) {
  $output['error'] = $ex->getMessage();
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
