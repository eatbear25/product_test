<?php
require __DIR__ . "/parts/db-connect.php";
$title = '首頁';
// ? 看看pageName是什麼
// $pageName = 'home'; 
?>

<?php include __DIR__ . '/parts/html-head.php' ?>
<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <h1>我的首頁</h1>
</div>

<?php include __DIR__ . '/parts/html-scripts.php' ?>
<?php include __DIR__ . '/parts/html-tail.php' ?>