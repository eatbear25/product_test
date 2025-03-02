<?php
require __DIR__ . "/parts/db-connect.php";
$title = '新增產品';
// $pageName = 'ab-add';
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
          <h5 class="card-title">新增商品</h5>
          <form name="addForm" novalidate onsubmit="sendData(event)">
            <div class="mb-3">
              <label for="name" class="form-label">姓名 **</label>
              <input type="text" class="form-control" id="name" name="name" required>
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">電郵 **</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="mobile" class="form-label">手機</label>
              <input type="text" class="form-control" id="mobile" name="mobile" pattern="09\d{8}">
              <div class="form-text"></div>
            </div>
            <div class="mb-3">
              <label for="birthday" class="form-label">生日</label>
              <input type="date" class="form-control" id="birthday" name="birthday">

            </div>
            <div class="mb-3">
              <label for="address" class="form-label">地址</label>
              <textarea class="form-control" id="address" name="address"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">新增</button>
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
        <h1 class="modal-title fs-5" id="exampleModalLabel">新增結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          資料新增成功
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">繼續新增資料</button>
        <a href="list.php" class="btn btn-primary">回列表頁</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/parts/html-scripts.php' ?>

<script>
  // 建立對應到 DOM 的 Modal 物件
  const myModal = new bootstrap.Modal('#exampleModal');

  // 取得欄位的參照
  const nameField = document.addForm.name;
  const emailField = document.addForm.email;

  function validateEmail(email) {
    const re =
      /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }


  const sendData = e => {
    e.preventDefault();
    // 恢復欄位的外觀
    nameField.style.border = '1px solid #CCC';
    nameField.nextElementSibling.innerHTML = '';
    emailField.style.border = '1px solid #CCC';
    emailField.nextElementSibling.innerHTML = '';

    // TODO: 欄位的資料檢查
    let isPass = true; // 有沒有通過檢查


    if (nameField.value.length < 2) {
      isPass = false;
      nameField.style.border = '2px solid red';
      nameField.nextElementSibling.innerHTML = '請填入正確的姓名';
    }

    if (!validateEmail(emailField.value)) {
      isPass = false;
      emailField.style.border = '2px solid red';
      emailField.nextElementSibling.innerHTML = '請填入正確的電子郵件信箱';
    }

    if (isPass) {
      // 如果全部要檢查的欄位都通過檢查
      const fd = new FormData(document.addForm);

      fetch('add-api.php', {
          method: 'POST',
          body: fd
        })
        .then(r => r.json())
        .then(result => {
          console.log(result);
          if (result.success) {
            myModal.show(); // 顯示 Modal
            return;
          }
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