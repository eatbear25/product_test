<?php
header('Content-Type: application/json');

$output = [
  'success' => false, # 有沒有成功上傳圖檔
  'error' => '',
  'file' => '', # 儲存的檔名
  'code' => 0,
];

$dir = __DIR__ . '/images/';

# 1.篩選可上傳的檔案類型, 2.決定副檔名
$extMap = [
  'image/jpeg' => '.jpg',
  'image/png' => '.png',
  'image/webp' => '.webp',
];

if (empty($_FILES['avatar'])) {
  # avatar 欄位沒有上傳檔案
  echo json_encode($output);
  exit;
}

if (! is_string($_FILES['avatar']['name'])) {
  # 必須只上傳一個檔案
  $output['code'] = 401;
  echo json_encode($output);
  exit;
}

if ($_FILES['avatar']['error'] != 0) {
  # 上傳過程發生錯誤
  $output['code'] = 405;
  echo json_encode($output);
  exit;
}

if (empty($extMap[$_FILES['avatar']['type']])) {
  # 上傳的檔案不符合要求的類型
  $output['code'] = 407;
  echo json_encode($output);
  exit;
}

$ext = $extMap[$_FILES['avatar']['type']]; # 對應到的副檔
$file = md5($_FILES['avatar']['name'] . uniqid()) . $ext;
$output['file'] = $file;

try {
  $output['success'] = move_uploaded_file(
    $_FILES['avatar']['tmp_name'],
    $dir .  $file
  );
} catch (Exception $ex) {
  $output['error'] = $ex->getMessage();
}

echo json_encode($output);
