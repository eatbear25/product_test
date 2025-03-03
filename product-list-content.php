<div class="container">
  <nav aria-label="Page navigation example">
    <ul class="pagination">
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
      </li>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item">
          <a class="page-link <?= $page == $i ? 'active' : '' ?>" href='?page=<?= $i ?>'><?= $i ?>
          </a>
        </li>
      <?php endfor ?>

      <li class=" page-item">
        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
      </li>

      <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#addProductModal">
        + 新增商品
      </button>

      <!-- 新增商品 Modal -->
      <div class="modal fade" id="addProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addProductModalLabel">新增商品</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- 表單內容 -->
            <div class="modal-body">
              <form name="addProductForm" novalidate onsubmit="sendData(event)">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="floatingInput" placeholder="請輸入產品名稱" name="name">
                  <label for="floatingInput">產品名稱</label>
                </div>

                <div class="form-floating mb-3">
                  <textarea class="form-control" placeholder="請輸入產品介紹" id="floatingTextarea2" style="height: 100px"
                    name="content"></textarea>
                  <label for="floatingTextarea2">介紹</label>
                </div>

                <div class="form-floating mb-3">
                  <select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example"
                    name="category">
                    <option selected disabled>請選擇</option>

                    <?php foreach ($category_rows as $row): ?>
                      <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php endforeach ?>

                  </select>
                  <label for="floatingSelectGrid">產品分類</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="number" class="form-control" id="floatingInput" placeholder="請輸入庫存數量" name="stock" min=0>
                  <label for="floatingInput">庫存數量</label>
                </div>

                <div class="form-floating mb-3">
                  <input type="number" class="form-control" id="floatingInput" placeholder="請輸入商品價格" name="price" min=0>
                  <label for="floatingInput">商品價格</label>
                </div>

                <div class="form-floating mb-3">
                  <select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example"
                    name="status">
                    <option value="1" selected>上架</option>
                    <option value="0">下架</option>
                  </select>
                  <label for="floatingSelectGrid">商品狀態</label>
                </div>

                <?php
                //TODO: 解決圖片上傳 
                ?>

                <!-- <div class="mb-3">
                  <label for="formFile" class="form-label">商品照片</label>
                  <input class="form-control" type="file" id="formFile" name="photo">
                </div> -->

                <!-- 結尾 -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                  <button type="submit" class="btn btn-danger">確認新增</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>

    </ul>
  </nav>
</div>

<div class="container-fluid">
  <table class="table">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">產品名稱</th>
        <th scope="col">介紹</th>
        <th scope="col">分類</th>
        <th scope="col">庫存</th>
        <th scope="col">價格</th>
        <th scope="col">商品狀態</th>
        <th scope="col">照片</th>
        <th scope="col">上架時間</th>
        <th scope="col">更新時間</th>
        <th scope="col">功能列表</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= $row['name'] ?></td>
          <td class="content-column">
            <!-- TODO: 確認怎麼顯示 -->
            <?php // mb_strimwidth($row['content'], 0, 25, '...') 
            ?>
            <?= $row['content']; ?>
          </td>
          <td><?= $row['category_name'] ?></td>
          <td><?= $row['stock'] ?></td>
          <td>$<?= number_format($row['price'], 2) ?>
          </td>
          <td>
            <button type="button" class="btn <?= $row['status'] == 1 ? 'btn-outline-success' : 'btn-outline-danger' ?>">
              <?= $row['status'] == 1 ? '上架' : '下架' ?>
            </button>
          </td>
          <td><?= $row['image'] ?></td>
          <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
          <td><?= date("Y-m-d", strtotime($row['updated_at'])) ?></td>
          <td>
            <a href="edit-product.php?id=<?= $row['id'] ?>" class="btn btn-warning">編輯</a>
            <a href="javascript: deleteOne(<?= $row['id'] ?>)" class="btn btn-danger">刪除</a>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<!-- 新增結果 Modal -->
<div class="modal fade" id="addResultModal" tabindex="-1" aria-labelledby="addResultModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addResultModalLabel">新增結果</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" role="alert">
          商品新增成功
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">繼續新增資料</button> -->
        <a href="product-list.php" class="btn btn-primary">回列表頁</a>
      </div>
    </div>
  </div>
</div>