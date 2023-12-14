<!-- <?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

include 'components/add_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tìm kiếm</title>

<!-- font awesome cdn link  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="css/style.css">

</head>

<body>

<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<!-- search form section starts  -->

<section class="search-form">
<form method="post" action="">
  <input type="text" name="search_box" placeholder="Điền vào từ khoá..." class="box">
  <button type="submit" name="search_btn" class="fas fa-search"></button>
</form>
<!-- search history  -->
<section class="search-history-images">
  <?php
  // Fetch the latest 3 search history images
  $select_latest_search = $conn->prepare("SELECT * FROM search_history ORDER BY timestamp DESC LIMIT 3");
  $select_latest_search->execute();

  if ($select_latest_search->rowCount() > 0) {
      while ($fetch_search = $select_latest_search->fetch(PDO::FETCH_ASSOC)) {
  ?>
          <div class="search-history-image">
              <img src="uploaded_img/<?= $fetch_search['image_path']; ?>" alt="<?= $fetch_search['search_term']; ?>">
              <p><?= $fetch_search['search_term']; ?></p>
          </div>
  <?php
      }
  }
  ?>
  <style>

.search-history-images {
display: flex;
justify-content: space-between;
margin-top: 20px;
flex-wrap: wrap;
}

.search-history-image {
text-align: center;
max-width: 100px; /* Điều chỉnh chiều rộng tối đa nếu cần */
margin-bottom: 10px;
}

.search-history-image img {
width: 100%;
height: auto;
border-radius: 5px;
object-fit: cover; /* Đảm bảo hình ảnh không bị méo */
}

.search-history-image p {
margin: 5px 0;
font-size: 14px;
}

  </style>
</section>
</section>


<!-- search form section ends -->

<section class="products" style="min-height: 100vh; padding-top:0;">

<div class="box-container">

   <?php
   if (isset($_POST['search_box']) || isset($_POST['search_btn'])) {
      $search_box = $_POST['search_box'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE '%{$search_box}%'");
      $select_products->execute();

      if ($select_products->rowCount() > 0) {
while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            // Insert search information into the search_history table
            $search_term = $search_box;
            $image_path = $fetch_products['image'];

            $insert_search = $conn->prepare("INSERT INTO search_history (search_term, image_path) VALUES (:search_term, :image_path)");
            $insert_search->bindParam(':search_term', $search_term);
            $insert_search->bindParam(':image_path', $image_path);
            $insert_search->execute();

   ?>
            <form action="" method="post" class="box">
               <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
               <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
               <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
               <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
               <a href="quick_view.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
               <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
               <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
               <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
               <div class="name"><?= $fetch_products['name']; ?></div>
               <div class="flex">
                  <div class="price"><span></span><?= number_format($fetch_products['price'], 0, '', '.'); ?>đ</div>
                  <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
               </div>
            </form>
   <?php
         }
      } else {
         echo '<p class="empty">Không tìm thấy sản phẩm!</p>';
      }
   }
   ?>
   

</div>

</section>











<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>

</html> -->