<?php
session_start();
include '../components/connect.php';


$admin_id = $_SESSION['admin_id'];
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$product_id]);

    // Redirect back to the products page after deleting
    header('location: admin_warehouse.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">    <link rel="stylesheet" href="../css/admin_style.css">
    <title>Kho Hàng</title>
</head>
<body>
    <style>
        h1{
            text-align: center;
            font-size: 5em;
        }
       
        .box img{
            width: 150px;
        }
        .option-btn{
            width: 150px;
        }
        .product-container {
            display: flex;
            justify-content: center; /* Căn giữa theo chiều ngang */
            flex-wrap: wrap;
        }

    </style>
<?php include '../components/admin_header.php' ?>
<section class="search-form">
<form method="post" action="">
  <input type="text" name="search_box" placeholder="Điền vào từ khoá..." class="box">
  <button type="submit" name="search_btn" class="fas fa-search"></button>
</form>

<?php
   if (isset($_POST['search_box']) || isset($_POST['search_btn'])) {
      $search_box = $_POST['search_box'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%'");
      $select_products->execute();

      if ($select_products->rowCount() > 0) {
while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            $search_term = $search_box;
            $image_path = $fetch_products['image'];

            $insert_search = $conn->prepare("INSERT INTO search_history (search_term, image_path) VALUES (:search_term, :image_path)");
            $insert_search->bindParam(':search_term', $search_term);
            $insert_search->bindParam(':image_path', $image_path);
            $insert_search->execute();

   ?>
        <div class="product-container">
            <form action="" method="post" class="box">
            <div class="box">
                    <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                    <div class="flex">
                    <div class="price"><span></span><?= number_format($fetch_products['price'], 0, '', '.'); ?><span>đ</span></div>
                    <div class="quantity">Còn Lại: <?= $fetch_products['quantity']; ?></div>
                    <div class="category"><?= $fetch_products['category']; ?></div>
                    </div>
                    <div class="name"><?= $fetch_products['name']; ?></div>
                    <div class="flex-btn">
                        <a href="update_product.php?update=<?= $fetch_products['id']; ?>"  class="bi bi-pencil-square">SỬA</a>
                        <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="bi bi-trash-fill"
                           onclick="return confirm('Xác nhận xoá sản phẩm?');">XOÁ</a>
                    </div>
            </div>
        </div>
            </form>
   <?php
         }
      } else {
         echo '<p class="empty">Không tìm thấy sản phẩm!</p>';
      }
   }
   ?>
<section class="show-products" style="padding-top: 0;">
<h1>Tất cả sản phẩm trong kho</h1>
    <div class="box-container">
        <?php
        $show_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
        $show_products->execute();
        if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                    <div class="flex">
                    <div class="price"><span></span><?= number_format($fetch_products['price'], 0, '', '.'); ?><span>đ</span></div>
                    <div class="quantity">Còn Lại: <?= $fetch_products['quantity']; ?></div>
                    <div class="category"><?= $fetch_products['category']; ?></div>
                    </div>
                    <div class="name"><?= $fetch_products['name']; ?></div>
                    <div class="flex-btn">
                        <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">SỬA</a>
                        <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn"
                           onclick="return confirm('Xác nhận xoá sản phẩm?');">XOÁ</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">Chưa có sản phẩm!</p>';
        }
        ?>
</body>
</html>