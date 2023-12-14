<?php

include 'components/connect.php';
include 'mail/sendmail.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];

}else{
   $user_id = '';
   $message[] = 'Vui lòng đăng nhập!';
   header('location:home.php', $message[] = 'Vui lòng đăng nhập!');
   $message[] = 'Vui lòng đăng nhập!';
};
if(isset($_POST['submit'])){
   $oid = $_POST['oid'];
   $oid = filter_var($oid, FILTER_SANITIZE_STRING);
   $get_email = $conn->prepare("SELECT `email` FROM `orders` WHERE id = ?");
   $get_email->execute([$oid]);
   if($get_email->rowCount() >0){
      $email = $get_email->fetch(PDO::FETCH_ASSOC);
      smtp_mailer($email['email'],'3 GUYS','ĐƠN HÀNG CỦA BẠN ĐÃ HUỶ THÀNH CÔNG');
   }
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$oid]);
   $message[] = 'Huỷ đơn hàng thành công!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đơn Hàng</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>Đơn hàng</h3>
   <p><a href="html.php">Trang chủ</a> <span> / Đơn hàng</span></p>
</div>

<section class="orders">

   <h1 class="title">Đơn hàng</h1>

   <div class="box-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">Hãy đăng nhập để xem đơn hàng</p>';
      }else{
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY  id DESC");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <form action="" method="post">
         <input type="hidden" name="oid" value="<?= $fetch_orders['id']; ?>">
         <p>Ngày đặt hàng: <span><?= $fetch_orders['placed_on']; ?></span></p>
         <p>Tên: <span><?= $fetch_orders['name']; ?></span></p>
         <p>Email: <span><?= $fetch_orders['email']; ?></span></p>
         <p>SĐT: <span><?= $fetch_orders['number']; ?></span></p>
         <p>Địa chỉ: <span><?= $fetch_orders['address']; ?></span></p>
         <p>Phương thức thanh toán: <span><?= $fetch_orders['method']; ?></span></p>
         <p>Đơn hàng: <span><?= $fetch_orders['total_products']; ?></span></p>
         <p>Giảm giá: <span><?= $fetch_orders['discount']; ?></span></p>
         <p>Tổng tiền: <span><?= number_format( $fetch_orders['total_price'], 0, '', '.'); ?>đ</span></p>
         <p>Trạng thái: <span style="color:<?php if($fetch_orders['payment_status'] == 'Chờ xác nhận'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
         <input type="submit" value="Huỷ đơn hàng" class="btn <?php if($fetch_orders['payment_status'] != 'Chờ xác nhận'){echo 'disabled';} ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit" onclick="return confirm('Xác nhận huỷ đơn hàng?');">
      </form>
   </div>
   <?php
      }
      }else{
         echo '<p class="empty">Bạn chưa có đơn hàng!</p>';
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
</html>