<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="home.php" class="logo">3 GUYS</a>

      <nav class="navbar">
         <a href="home.php">TRANG CHỦ</a>
         <a href="menu.php">SẢN PHẨM</a>
         <?php
         if (isset($_SESSION['user_id'])) {
            echo '<a href="orders.php">ĐƠN HÀNG</a>';
         }
         ?>
         <a href="about.php">3 GUYS</a>
         <a href="contact.php">LIÊN HỆ</a>
      </nav>

      <div class="icons">
         <?php
         $count_cart_items = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
         $count_cart_items->execute([$user_id]);
         $total_cart_items = $count_cart_items->rowCount();
         ?>
         <a href="search.php"><i class="fas fa-search"></i></a>
         <?php
         if (isset($_SESSION['user_id'])) {
            echo '<a href="cart.php"><i class="fas fa-shopping-cart"></i><span>['.$total_cart_items.']</span></a>';
         }
         ?>
         <!-- Add link to favorite products page -->
         <a href="favorite.php" id="heart-btn"><i class="fas fa-heart"></i></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php
         $select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
         $select_profile->execute([$user_id]);
         if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
            <p class="name"><?= $fetch_profile['name']; ?></p>
            <a href="profile.php" class="btn-nav" style="width: 100%;">Thông Tin</a>
            <a href="components/user_logout.php" onclick="return confirm('Xác nhận đăng xuất khỏi trang web?');" class="del-nav" style="width: 100%;">Đăng Xuất</a>
         <?php
         } else {
         ?>
            <p class="name">Hãy đăng nhập!</p>
            <a href="login.php" class="btn-nav" style="width: 100%;">Đăng Nhập</a>
            <a href="register.php" class="btn-nav" style="width: 100%;">Đăng Kí</a>
         <?php
         }
         ?>
      </div>

   </section>

</header>