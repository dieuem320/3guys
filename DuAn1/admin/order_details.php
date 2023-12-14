<?php
   include '../components/connect.php';

   include '../components/connect.php';
   include '../mail/sendmail.php';
   
   session_start();
   if (isset($_GET['id'])) {
      $order_id = $_GET['id'];
      $get_order_details = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
      $get_order_details->execute([$order_id]);

      if ($get_order_details->rowCount() > 0) {
         $order_details = $get_order_details->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Chi Tiết Đơn Hàng</title>
   <!-- Thêm các liên kết CSS hoặc bất kỳ phần tử khác cần thiết -->
      <!-- font awesome cdn link  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
<?php include '../components/admin_header.php' ?>
<style>
    .box {
   background-color: #fff; /* Màu nền box */
   padding: 20px;
   border-radius: 8px;
   box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
   max-width: 600px;
   width: 100%;
   text-align: center;
   font-size: 24px; /* Kích thước chữ lớn */
   color: #333; /* Màu chữ */
   font-size: 18px; /* Kích thước chữ cho các đoạn văn bản */
   line-height: 1.6;
   color: #666; /* Màu chữ */
   margin: 0 auto; /* Căn giữa theo chiều ngang */
}
  h1{
    text-align: center;
    font-size: 24px; /* Kích thước chữ lớn */
   color: #333; /* Màu chữ */
  }
</style>
<!-- placed orders section starts  -->
   <h1>Chi Tiết Đơn Hàng số <?= $order_details['id']; ?></h1>
   <div class="box">
   <p><strong>ID Khách Hàng:</strong> <?= $order_details['user_id']; ?></p>
   <p><strong>Ngày đặt hàng:</strong> <?= $order_details['placed_on']; ?></p>
   <p><strong>Tên:</strong> <?= $order_details['name']; ?></p>
   <p><strong>Email:</strong> <?= $order_details['email']; ?></p>
   <p><strong>SĐT:</strong> <?= $order_details['number']; ?></p>
   <p><strong>Địa chỉ:</strong> <?= $order_details['address']; ?></p>
   <p><strong>Đơn Hàng:</strong> <?= $order_details['total_products']; ?></p>
   <p><strong>Thành tiền:</strong> <?= number_format($order_details['total_price'], 0, '', '.') . 'đ'; ?></p>
   <p><strong>Phương thức thanh toán:</strong> <?= $order_details['method']; ?></p>
   <!-- Thêm các thông tin khác cần hiển thị -->
   </div>
   <!-- Thêm các liên kết JS hoặc bất kỳ phần tử JS khác cần thiết -->

</body>
</html>
<?php
      } else {
         echo 'Không tìm thấy đơn hàng.';
      }
   } else {
      echo 'Yêu cầu không hợp lệ.';
   }
?>