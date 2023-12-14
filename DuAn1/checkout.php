<?php

include 'components/connect.php';
include 'mail/sendmail.php';
include "helper.php";

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
};

if (isset($_POST['submit'])) {

   $name = $_POST['name'];
   $name = sanitizeInput($name);
   $number = $_POST['number'];
   $number = sanitizeInput($number);
   $email = $_POST['email'];
   $email = sanitizeInput($email);
   $method = $_POST['method'];
   $method = sanitizeInput($method);
   $address = $_POST['address'];
   $address = sanitizeInput($address);
   $name = $_POST['name'];
   $name = sanitizeInput($name);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   // check ton kho =>
   if (($_POST['code']) != "") {
      $code = $_POST['code'];
      $code = filter_var($code, FILTER_SANITIZE_STRING);
      $get_amount = $conn->prepare("SELECT amount FROM discount WHERE code = ?");
      $get_amount->execute([$code]);
      if ($get_amount->rowCount() > 0) {
         $amount = $get_amount->fetch(PDO::FETCH_ASSOC);
         $total_price = $total_price - ($total_price / 100 * $amount['amount']);
         $discount = $amount['amount'] . '%';
      } else {
         $discount = "Không";
      }
   } else {
      $discount = "Không";
   }



   $check_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if ($check_cart->rowCount() > 0) {

      if ($address == '') {
         $message[] = 'Xin mời nhập địa chỉ nhận hàng!';
      } else {

         // Thêm code để giảm số lượng sản phẩm từ bảng products
         $decrease_product_quantity = $conn->prepare("UPDATE products SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?");

         $select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
         $select_cart->execute([$user_id]);

         while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
            // Lấy thông tin sản phẩm từ bảng products
            // echo "Quantity: " . $fetch_cart['quantity'] . ", Product ID: " . $fetch_cart['pid'] . "<br>";
            $get_product_info = $conn->prepare("SELECT id, quantity FROM products WHERE id = ?");
            $get_product_info->execute([$fetch_cart['pid']]);

            if ($get_product_info->rowCount() > 0) {

               // Nếu sản phẩm tồn tại, giảm số lượng
               $product_info = $get_product_info->fetch(PDO::FETCH_ASSOC);
               $new_quantity = $product_info['quantity'] - $fetch_cart['quantity'];
               // Kiểm tra xem số lượng còn đủ hay không
               if ($new_quantity >= 0) {
                  // Thực hiện câu truy vấn UPDATE trực tiếp từ bảng products
                  $update_product_quantity = $decrease_product_quantity; // Reuse the prepared statement
$update_product_quantity->execute([$fetch_cart['quantity'], $product_info['id']]);
               } else {
                  $message = array();
                  array_push($message, "Sản phẩm có ID " . $product_info['id'] . " trong kho không đủ!");
                  // Số lượng không đủ, hiển thị thông báo
                  // echo "Sản phẩm có ID " . $product_info['id'] . " trong kho không đủ!";
                  // echo "Số lượng đặt hàng: " . $fetch_cart['quantity'] . ", Số lượng hiện tại trong kho: " . $product_info['quantity'];
               }
              
            }
         }

         if (count($message) == 0) {

            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, discount) VALUES(?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $discount]);

            // Lấy ID của đơn hàng vừa được thêm
            $order_id = $conn->lastInsertId();
            $email_body = '';
            $email_subject = '';
            // Thêm nội dung email với thông tin đơn hàng
            $email_subject = 'ĐẶT HÀNG THÀNH CÔNG!';
            $email_body = 'Cảm ơn bạn đã đặt hàng! Nhân viên của chúng tôi sẽ liên hệ với bạn để xác nhận đơn hàng.<br>';
            $email_body .= '<strong>Thông tin đơn hàng:</strong><br>';
            $email_body .= '<strong>ID Đơn hàng:</strong> ' . $order_id . '<br>';
            $email_body .= '<strong>Tên người nhận:</strong> ' . $name . '<br>';
            $email_body .= '<strong>Số điện thoại:</strong> ' . $number . '<br>';
            $email_body .=    '<strong>Email:</strong> ' . $email . '<br>';
            $email_body .= '<strong>Địa chỉ:</strong> ' . $address . '<br>';
            $email_body .= '<strong>Tổng số sản phẩm:</strong> ' . $total_products . '<br>';
            $email_body .= '<strong>Tổng giá:</strong> ' . number_format($total_price, 0, '', '.') . 'đ<br>';
            $email_body .= '<strong>Giảm giá:</strong> ' . $discount . '<br>';

            // Thêm thông tin chi tiết sản phẩm vào email
            $email_body .= 'Danh sách sản phẩm:' . PHP_EOL;
            $select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
               $email_body .= '- ' . $fetch_cart['name'] . ' x ' . $fetch_cart['quantity'] . ' giá: ' . number_format($fetch_cart['price'], 0, '', '.') . 'đ' . PHP_EOL;
            }

            // Gửi email
            smtp_mailer($email, $email_subject, $email_body);

            $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $delete_cart->execute([$user_id]);

            $message[] = 'Đặt hàng thành công!';
         }
      }
} else {
      $message[] = 'Giỏ hàng không có sản phẩm!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đặt Hàng</title>

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
      <h3>Đặt Hàng</h3>
   </div>

   <section class="checkout">

      <h1 class="title">Thông tin đơn hàng</h1>

      <form action="" method="post">

         <div class="cart-items">
            <h3>Danh sách sản phẩm</h3>
            <?php
            $grand_total = 0;
            $cart_items[] = '';
            $select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['name'] . ' (' . number_format($fetch_cart['price'], 0, '', '.') . 'đ x quantity' . $fetch_cart['quantity'] . ') & ';
                  $total_products = implode($cart_items);
                  $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
            ?>
                  <p><span class="name"><?= $fetch_cart['name']; ?></span><span class="price"><?= number_format($fetch_cart['price'], 0, '', '.'); ?>đ x <?= $fetch_cart['quantity']; ?></span></p>
            <?php
               }
            } else {
               echo '<p class="empty">Chưa có sản phẩm trong giỏ hàng!</p>';
            }
            ?>
            <p class="grand-total"><span class="name">Tổng Tiền :</span><span class="price"><?= number_format($grand_total, 0, '', '.'); ?>đ</span></p>
            <a href="cart.php" class="btn">Xem giỏ hàng</a>
         </div>

         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
         <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
         <input type="hidden" name="number" value="<?= $fetch_profile['number'] ?>">
         <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
         <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

         <div class="user-info">
            <h3>Thông Tin Người Nhận</h3>
            <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
            <p><i class="fas fa-phone"></i><span><?= $fetch_profile['number'] ?></span></p>
<p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
            <a href="update_profile.php" class="btn">Cập Nhật Thông Tin</a>
            <h3>Địa Chỉ Nhận Hàng</h3>
            <p><i class="fas fa-map-marker-alt"></i><span><?php if ($fetch_profile['address'] == '') {
                                                               echo 'Xin hãy cập nhật địa chỉ!';
                                                            } else {
                                                               echo $fetch_profile['address'];
                                                            } ?></span></p>
            <a href="update_address.php" class="btn">Cập Nhật Địa Chỉ</a>
            <select name="method" class="box" required>
               <option value="" disabled selected>Lựa chọn phương thức thanh toán</option>
               <option value="Thanh toán khi nhận hàng">Thanh toán khi nhận hàng</option>
               <option value="Credit Card">Credit Card</option>
               <option value="ATM">ATM</option>
               <option value="MOMO">Thanh toán MOMO ATM</option>
            </select>

            <form class="" method="POST" target="_blank" enctype="application/-www-form-urlencoded" action="/momo//xulithanhtoanmomo.php">
               <input type="submit" name="momo" value="Thanh toán MOMO QRcode" class="btn btn-danger">
            </form>





            <div class="btn">Mã giảm giá</div>
            <input type="text" value="" name="code" class="box">
            <input type="submit" value="Đặt Hàng" class="btn <?php if ($fetch_profile['address'] == '') {
                                                                  echo 'disabled';
                                                               } ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit" onclick="return confirm('Xác nhận đặt hàng?');">
         </div>

      </form>

   </section>









   <!-- footer section starts  -->
   <?php include 'components/footer.php'; ?>
   <!-- footer section ends -->






   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>