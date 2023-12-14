<?php

include 'components/connect.php';  

session_start();
$_SESSION["message"] = null;

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';

if(isset($_GET['pid'])) {
   $pid = $_GET['pid'];

   // Kiểm tra xem sản phẩm đã tồn tại trong trang yêu thích của người dùng chưa
   $checkFavorite = $conn->prepare("SELECT * FROM favorite WHERE user_id = ? AND product_id = ?");
   $checkFavorite->execute([$user_id, $pid]);

   if ($checkFavorite->rowCount() == 0) {
       // Nếu chưa tồn tại, thêm sản phẩm vào trang yêu thích
       $addToFavorite = $conn->prepare("INSERT INTO favorite (user_id, product_id) VALUES (?, ?)");
       $addToFavorite->execute([$user_id, $pid]);
       echo "Sản phẩm đã được thêm vào trang yêu thích!";
   } else {
       echo "Sản phẩm đã tồn tại trong trang yêu thích!";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>



<section class="hero">

   <div class="swiper hero-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <div class="content">
               <span>Đặt hàng online</span>
               <h3>Giao hàng tận nhà</h3>
               <a href="menu.php" class="btn">Xem sản phẩm</a>
            </div>
            <div class="image">
               <img src="images/home-img-1.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>Giá tốt - Ưu đãi</span>
               <h3>Chính hãng 100%</h3>
               <a href="menu.php" class="btn">Xem sản phẩm</a>
            </div>
            <div class="image">
               <img src="images/home-img-2.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>Uy tín - Chất lượng</span>
               <h3>Bảo hành trọn đời</h3>
               <a href="menu.php" class="btn">Xem sản phẩm</a>
            </div>
            <div class="image">
               <img src="images/home-img-3.png" alt="">
            </div>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<section class="category">

   <h1 class="title">Danh Mục</h1>

   <div class="box-container">

      <a href="category.php?category=iphone" class="box">
         <img src="images/cat-1.png" alt="">
         <h3>IPhone</h3>
      </a>

      <a href="category.php?category=ipad" class="box">
         <img src="images/cat-2.png" alt="">
         <h3>IPad</h3>
      </a>

      <a href="category.php?category=etc" class="box">
         <img src="images/cat-3.png" alt="">
         <h3>Phụ Kiện</h3>
      </a>

      <a href="category.php?category=earphone" class="box">
         <img src="images/cat-4.png" alt="">
         <h3>Tai Nghe</h3>
      </a>

   </div>

</section>




<section class="products">

   <h1 class="title">Sản Phẩm Mới</h1>

   <div class="box-container">
   
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
         $select_products->execute();
         if($select_products->rowCount() > 0){
            while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
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
            <div class="price"><span></span><?= number_format( $fetch_products['price'], 0, '', '.'); ?>đ</div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">Chưa có sản phẩm!</p>';
         }
      ?>

   </div>

   <div class="more-btn">
      <a href="menu.php" class="btn">Xem thêm</a>
   </div>

</section>


















<?php include 'components/footer.php'; ?>


<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".hero-slider", {
   loop:true,
   grabCursor: true,
   effect: "flip",
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
});

</script>

</body>
</html>