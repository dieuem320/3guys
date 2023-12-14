<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <section class="flex">

      <a href="dashboard.php" class="logo"><span>Quản Trị Viên</span></a>

      <nav class="navbar">
       
         <a href="products.php">Sản phẩm</a>
         <a href="admin_categoris.php">Danh mục</a>
         <a href="placed_orders.php">Đơn hàng</a>
         <a href="admin_warehouse.php">Kho Hàng</a>
         <a href="admin_accounts.php">Quản trị viên</a>
         <a href="users_accounts.php">Người dùng</a>
         <a href="messages.php">Lời nhắn</a>
         <a href="discount.php">Giảm giá</a>
         
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <!-- <?php
            $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?> -->
         <p><?= $fetch_profile['name']; ?></p>
         <a href="update_profile.php" class="btn">Cập nhập thông tin</a>
         <a href="../components/admin_logout.php" onclick="return confirm('Xác nhận đăng xuất?');" class="delete-btn">Đăng xuất</a>
      </div>

   </section>

</header>