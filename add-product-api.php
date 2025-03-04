<?php
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

// * 取得表單資料
$name = trim($_POST['name'] ?? '');
$content = trim($_POST['content'] ?? '');
$category_id = $_POST['category'] ?? null;
$stock = $_POST['stock'] ?? null;
$price = $_POST['price'] ?? null;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

$isPass = true;

// * 文字欄位的基本驗證
// if (empty($name)) {
//   $isPass = false;
//   $output['errorFields']['name'] = '產品名稱為必填欄位';
// }

// if (empty($content)) {
//   $isPass = false;
//   $output['errorFields']['content'] = '產品描述為必填欄位';
// }

// if (empty($category_id) || !is_numeric($category_id)) {
//   $isPass = false;
//   $output['errorFields']['category'] = '請選擇有效的分類';
// }

// if (empty($stock) || !is_numeric($stock)) {
//   $isPass = false;
//   $output['errorFields']['stock'] = '庫存數量必須是數字';
// }

// if (empty($price) || !is_numeric($price)) {
//   $isPass = false;
//   $output['errorFields']['price'] = '價格必須是數字';
// }

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

// * 若未通過驗證，回傳 JSON 並停止執行
if (!$isPass) {
  echo json_encode($output, JSON_UNESCAPED_UNICODE);
  exit;
}

// * 上傳至資料庫（確保資料一致性）
try {
  // $pdo->beginTransaction(); // 開始交易

  $sql = "INSERT INTO 
  `product` (`name`, `content`, `category_id`, `stock`, `price`, `status`, `image`) 
  VALUES (?, ?, ?, ?, ?, ?, ?)";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $name,
    $content,
    $category_id,
    $stock,
    $price,
    $status,
    $image_name // ✅ 把圖片檔名存入資料庫
  ]);

  $output['success'] = !!$stmt->rowCount();
  $output['id'] = $pdo->lastInsertId(); // 取得新增的產品 ID

  // $pdo->commit(); // 交易提交
} catch (PDOException $ex) {
  // $pdo->rollBack(); // 交易回滾
  $output['error'] = '資料庫錯誤：' . $ex->getMessage();
}

echo json_encode($output, JSON_UNESCAPED_UNICODE);
