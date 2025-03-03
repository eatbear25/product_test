<?php
$fieldName = 'photos'; // photos[]


$dir = __DIR__ . '/images/'; # 存放檔案的資料夾
$exts = [   # 檔案類型的篩選
  'image/jpeg' => '.jpg',
  'image/png' =>  '.png',
  'image/webp' => '.webp',
];

# 輸出的格式
$output = [
  'success' => false,
  'files' => []
];

if (!empty($_FILES) and !empty($_FILES[$fieldName])) {

  if (is_array($_FILES[$fieldName]['name'])) {    # 是不是陣列
    foreach ($_FILES[$fieldName]['name'] as $i => $name) {
      if (!empty($exts[$_FILES[$fieldName]['type'][$i]]) and $_FILES[$fieldName]['error'][$i] == 0) {
        $ext = $exts[$_FILES[$fieldName]['type'][$i]]; # 副檔名
        $f = sha1($name . uniqid() . rand()); # 隨機的主檔名
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'][$i], $dir . $f . $ext)) {
          $output['files'][] = $f . $ext;  // array push
        }
      }
    }
    if (count($output['files'])) {
      $output['success'] = true;
    }
  }
}
header('Content-Type: application/json');
echo json_encode($output);
