<?php

include '../components/connect.php';
include '../mail/sendmail.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
};

if(isset($_POST['update_payment'])){

   $order_id = $_POST['order_id'];
   $order_id = filter_var($order_id, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $payment_status = $_POST['payment_status'];
   $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_status->execute([$payment_status, $order_id]);
   if($payment_status == "Đã xác nhận"){
      smtp_mailer($email,'BIG CITY STORE','ĐƠN HÀNG CỦA BẠN ĐÃ ĐƯỢC XÁC NHẬN');
      $message[] = 'Cập nhật đơn hàng thành công!';
   }else{
      smtp_mailer($email,'BIG CITY STORE','ĐƠN HÀNG CỦA BẠN ĐÃ GIAO HÀNG THÀNH CÔNG');
      $message[] = 'Cập nhật đơn hàng thành công!';
   }
   
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $get_email = $conn->prepare("SELECT `email` FROM `orders` WHERE id = ?");
   $get_email->execute([$delete_id]);
   $email = $get_email->fetch(PDO::FETCH_ASSOC);
   smtp_mailer($email['email'],'BIG CITY STORE','ĐƠN HÀNG CỦA BẠN ĐÃ BỊ HUỶ');
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đơn hàng</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php' ?>

<!-- placed orders section starts  -->

<section class="placed-orders">

   <h1 class="heading">Quản lý đơn hàng</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY id DESC");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> ID Khách Hàng : <span><?= $fetch_orders['user_id']; ?></span> </p>
      <p> Ngày đặt hàng : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Tên : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> SĐT : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Địa chỉ : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Đơn Hàng : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Thành tiền : <span><?=number_format( $fetch_orders['total_price'], 0, '', '.') ; ?>đ</span> </p>
      <p> Phương thức thanh toán : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="POST">
                <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                <input type="hidden" name="email" value="<?= $fetch_orders['email']; ?>">
                <select name="payment_status" class="drop-down">
                    <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
                    <option value="Đã xác nhận">Đã xác nhận</option>
                    <option value="Đã giao hàng">Đã giao hàng</option>
                </select>
                <div class="flex-btn">
                    <input type="submit" value="Cập nhật đơn hàng" class="btn" name="update_payment">
                    <a href="order_details.php?id=<?= $fetch_orders['id']; ?>" class="details-btn">Chi tiết đơn hàng</a>
                    <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Xác nhận huỷ đơn hàng?');">Huỷ đơn hàng</a>
                </div>
            </form>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">Chưa có đơn hàng!</p>';
   }
   ?>

   </div>

</section>

<!-- placed orders section ends -->









<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

</body>
</html>