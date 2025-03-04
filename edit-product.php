<?php
// require __DIR__ . "/parts/admin-required.php"; # 需要管理者權限
require __DIR__ . "/parts/db-connect.php";
// $title = '編輯通訊錄';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  # 讀取單筆資料
  $sql = "SELECT * FROM product WHERE id=$id ";
  $row = $pdo->query($sql)->fetch();
}

if (empty($row)) {
  header("Location: product-list.php"); # 沒拿到該筆資料時, 回列表頁
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
//   "row" => $row

// ], JSON_UNESCAPED_UNICODE);
?>


<?php include __DIR__ . '/parts/html-head.php' ?>

<style>
  form .form-text {
    color: red;
  }
</style>

<?php include __DIR__ . '/parts/html-navbar.php' ?>

<div class="container">
  <div class="row">
    <div class="col-6">
      <div class="card">


        <div class="card-body">
          <h5 class="card-title">編輯商品</h5>
          <form name="editProductForm" novalidate onsubmit="sendData(event)">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
              <label for="" class="form-label">商品編號</label>
              <input type="text" class="form-control"
                value="<?= $row['id'] ?>" disabled>
            </div>

            <div class="mb-3">
              <label for="name" class="form-label">產品名稱 **</label>
              <input type="text" class="form-control" id="name" name="name"
                value="<?= htmlentities($row['name']) ?>" required>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="content" class="form-label">介紹 **</label>
              <textarea class="form-control" name="content" id="content" required><?= $row['content'] ?></textarea>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="category" class="form-label">產品分類</label>
              <select class="form-select" id="category" name="category">
                <option selected disabled>請選擇</option>
                <!-- TODO: 思考SELECT怎麼選 -->
                <!-- 
                分類的id == 取得的資料$row['category_id']
                $row['id']==$row['category_id'] ? 'selected':'' -->
                <?php foreach ($category_rows as $r): ?>
                  <option value="<?= $r['id'] ?>" <?= $r['id'] == $row['category_id'] ? 'selected' : '' ?>>
                    <?= $r['name'] ?>
                  </option>
                <?php endforeach ?>
              </select>
              <div class="form-text"></div>
            </div>

            <div class="mb-3">
              <label for="stock" class="form-label">庫存數量</label>
              <input type="number" class="form-control" id="stock" name="stock"
                value="<?= $row['stock'] ?>">
            </div>

            <div class="mb-3">
              <label for="price" class="form-label">商品價格</label>
              <input type="number" class="form-control" id="price" name="price"
                value="<?= $row['price'] ?>">
            </div>

            <div class="mb-3">
              <label for="status" class="form-label">商品狀態</label>
              <select class="form-select" id="status" name="status">
                <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>上架</option>
                <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>下架</option>
              </select>
            </div>

            <!-- 圖片區 -->
            <div class="mb-3">
              <label for="photo" class="form-label">商品照片</label>
              <input class="form-control" type="file" name="photo" accept="image/png,image/jpeg" />
              <div class="form-text"></div>
              <br />
              <img src="./images/<?= $row['image'] ?>" alt="" id="preview" width="300" />
            </div>



            <button type="submit" class="btn btn-primary">修改</button>
          </form>

        </div>


      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">修改結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          資料修改成功
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">繼續編輯</button>
        <a href="javascript: myBack() " class="btn btn-primary">回列表頁</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  // 建立對應到 DOM 的 Modal 物件
  const myModal = new bootstrap.Modal('#exampleModal');
  const modalInfo = document.querySelector('#exampleModal .alert');

  // 取得欄位的參照
  const nameField = document.editProductForm.name;
  const emailField = document.editProductForm.email;

  const myBack = () => {
    console.log('document.referer:', document.referrer);

    if (document.referrer) {
      location.href = document.referrer
    } else {
      location.href = 'product-list.php'
    }
  }

  const sendData = e => {
    e.preventDefault();
    // * 上傳圖片預覽
    const photo = document.addProductForm.photo;
    const preview = document.querySelector("#preview");

    // * 取得欄位的參照
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

    // 恢復欄位的外觀
    // nameField.style.border = '1px solid #CCC';
    // nameField.nextElementSibling.innerHTML = '';
    // emailField.style.border = '1px solid #CCC';
    // emailField.nextElementSibling.innerHTML = '';

    // TODO: 欄位的資料檢查
    let isPass = true; // 有沒有通過檢查


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

    if (isPass) {
      // 如果全部要檢查的欄位都通過檢查
      const fd = new FormData(document.editProductForm);

      fetch('edit-product-api.php', {
          method: 'POST',
          body: fd
        })
        .then(r => r.json())
        .then(result => {
          console.log(result);
          if (result.success) {
            modalInfo.classList.add('alert-success');
            modalInfo.classList.remove('alert-warning');
            modalInfo.innerHTML = "修改成功";
          } else {
            modalInfo.classList.remove('alert-success');
            modalInfo.classList.add('alert-warning');
            modalInfo.innerHTML = "資料沒有修改";
          }

          myModal.show(); // 顯示 Modal

          if (result.error) {
            alert(result.error);
          } else {
            for (let k in result.errorFields) {
              const el = document.querySelector(`#${k}`);
              if (el) {
                el.style.border = '2px solid red';
                el.nextElementSibling.innerHTML = result.errorFields[k];
              }
            }
          }
        })
        .catch(ex => {
          console.warn('Fetch 出錯了!');
          console.warn(ex);
        })
    }

  }
</script>

<?php include __DIR__ . '/parts/html-tail.php' ?>