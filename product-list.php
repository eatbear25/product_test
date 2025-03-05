<?php
require __DIR__ . "/parts/db-connect.php";
$title = '商品管理';
// ? 看看pageName是什麼
// $pageName = 'ab-list';

// * 看當下頁碼是多少
$page = isset($_GET["page"])  ? $_GET["page"] : 1;

if ($page < 1) {
  header('Location: ?page=1');
  exit;
}

// TODO: 搜尋篩選功能

// * 算出有幾頁
$perPage = 5;

$t_sql = " SELECT count(*) FROM `product` ";
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0];

$totalPages = 0; # 總頁數的預設值
$rows = []; # 頁面資料的預設值

// * 確認有資料才進這裡 (totalRows: 計算總筆數)
if ($totalRows) {
  $totalPages = ceil($totalRows / $perPage);

  if ($page > $totalPages) {
    header("Location: ?page={$totalPages}");
    exit;
  }

  // * 取得頁面資料
  // $sql = sprintf("SELECT * FROM `product` ORDER BY id DESC LIMIT %s, %s", ($page - 1) * $perPage, $perPage);

  $sql = sprintf(
    "
    SELECT product.*, category.name AS category_name
    FROM product
    INNER JOIN category ON product.category_id=category.id
    ORDER BY product.id DESC 
    LIMIT %s, %s",
    ($page - 1) * $perPage,
    $perPage
  );

  try {
    $rows = $pdo->query($sql)->fetchAll();
  } catch (PDOException $ex) {
    echo '<h1>' . $ex->getMessage() . '</h1>';
    echo '<h2>' . $ex->getCode() . '</h2>';
  }
}

// * 取得產品分類資料
$category_sql = " SELECT * FROM `category` ";

try {
  $category_rows = $pdo->query($category_sql)->fetchAll();
} catch (PDOException $ex) {
  echo '<h1>' . $ex->getMessage() . '</h1>';
  echo '<h2>' . $ex->getCode() . '</h2>';
}

// echo json_encode([
//   // "page" => $page,
//   // "totalPages" => $totalPages,
//   // "rows" => $rows
//   "category_rows" => $category_rows
// ], JSON_UNESCAPED_UNICODE);

?>


<?php include __DIR__ . '/parts/html-head.php' ?>

<style>
  .content-column {
    width: 250px;
  }
</style>

<?php include __DIR__ . '/parts/html-navbar.php' ?>

<?php include __DIR__ . '/product-list-content.php' ?>

<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  // * 建立對應到 DOM 的 Modal 物件
  const addModal = new bootstrap.Modal('#addProductModal');
  const addResultModal = new bootstrap.Modal('#addResultModal');
  // const addModal = new bootstrap.Modal(document.getElementById('addProductModal'));
  // const addResultModal = new bootstrap.Modal(document.getElementById('addResultModal'));


  // * 上傳圖片預覽
  const photo = document.addProductForm.photo;
  const preview = document.querySelector("#preview");

  // * 取得欄位的參照
  // const nameField = document.addForm.name;
  // const emailField = document.addForm.email;
  const photoField = document.addProductForm.photo;

  photo.addEventListener("change", (e) => {
    photoField.style.border = '1px solid #CCC';
    photoField.nextElementSibling.innerHTML = '';

    if (photo.files.length) {
      // 同步的方式載入檔案的內容預覽
      preview.src = URL.createObjectURL(photo.files[0]);
    } else {
      preview.src = "";
    }
  });

  const sendData = (e) => {
    e.preventDefault();
    event.stopPropagation();

    // 恢復欄位的外觀
    // nameField.style.border = '1px solid #CCC';
    // nameField.nextElementSibling.innerHTML = '';
    // emailField.style.border = '1px solid #CCC';
    // emailField.nextElementSibling.innerHTML = '';

    // TODO: 欄位的資料檢查
    let isPass = true; // 有沒有通過檢查

    // * 新增表單的前台驗證
    // if (nameField.value.length < 2) {
    //   isPass = false;
    //   nameField.style.border = '2px solid red';
    //   nameField.nextElementSibling.innerHTML = '請填入正確的姓名';
    // }

    // if (!validateEmail(emailField.value)) {
    //   isPass = false;
    //   emailField.style.border = '2px solid red';
    //   emailField.nextElementSibling.innerHTML = '請填入正確的電子郵件信箱';
    // }

    // 前端先檢查，避免沒選檔案就發送請求
    // if (!photo.files.length) {
    //   isPass = false;
    //   photoField.style.border = '2px solid red';
    //   photoField.nextElementSibling.innerHTML = '請選擇一張圖片';
    // }


    if (isPass) {
      // 如果全部要檢查的欄位都通過檢查
      const fd = new FormData(document.addProductForm);

      fetch('add-product-api.php', {
          method: 'POST',
          body: fd
        })
        .then(res => res.json())
        .then(result => {
          console.log("API 回應結果 (fetch 成功執行)", result); // ✅ 檢查 fetch 是否有回應

          if (result.success) {
            console.log("新增成功!!!!");
            addModal.hide(); // 隱藏 新增商品 Modal
            addResultModal.show(); // 顯示 新增結果 Modal
            document.addProductForm.reset();

            return;
          }
          // if (result.error) {
          //   alert(result.error);
          // } else {
          //   for (let k in result.errorFields) {
          //     const el = document.querySelector(`#${k}`);
          //     if (el) {
          //       el.style.border = '2px solid red';
          //       el.nextElementSibling.innerHTML = result.errorFields[k];
          //     }
          //   }
          // }
        })
        .catch(ex => {
          console.warn('Fetch 出錯了!');
          console.warn(ex);
        })
    }

  }

  const deleteOne = (id) => {
    // question: 1. 若要在詢問時呈現名字? 2. 點選後在詢問時整列要呈現明顯的底色
    if (confirm(`確定要刪除編號為 ${id} 的資料嗎?`)) {
      location.href = `del-product.php?id=${id}`;
    }
  }

  // * 監聽新增成功的 Modal 何時關閉
  document.getElementById('addResultModal').addEventListener('hidden.bs.modal', function() {
    location.reload(); // ✅ 自動刷新頁面，讓新商品出現
  });

  // * 監聽新增表單，每次關閉時就清空表單內容
  document.getElementById('addProductModal').addEventListener('hidden.bs.modal', function() {
    document.addProductForm.reset(); // 清空表單
    document.getElementById('preview').src = ''; // 清空圖片預覽
  });

  // * 表單驗證區
  // (function() {
  //   'use strict'

  //   // Fetch all the forms we want to apply custom Bootstrap validation styles to
  //   var forms = document.querySelectorAll('.needs-validation')

  //   // Loop over them and prevent submission
  //   Array.prototype.slice.call(forms)
  //     .forEach(function(form) {
  //       form.addEventListener('submit', function(event) {
  //         if (!form.checkValidity()) {
  //           event.preventDefault()
  //           event.stopPropagation()
  //         }

  //         form.classList.add('was-validated')
  //       }, false)
  //     })
  // })()
</script>

<?php include __DIR__ . '/parts/html-tail.php' ?>