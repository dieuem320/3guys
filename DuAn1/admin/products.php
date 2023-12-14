<?php
session_start();
include '../components/connect.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$message = array();

if (isset($_POST['add_product'])) {
    $names = $_POST['name'];
    $prices = $_POST['price'];
    $categories = $_POST['category'];
    $images = $_FILES['image']['name'];
    $quantities = $_POST['quantity'];

    for ($i = 0; $i < count($names); $i++) {
        // Lấy giá trị từ mảng tương ứng
        $name = filter_var($names[$i], FILTER_SANITIZE_STRING);
        $price = filter_var($prices[$i], FILTER_SANITIZE_STRING);
        $category = filter_var($categories[$i], FILTER_SANITIZE_STRING);
        $image = $_FILES['image']['name'][$i]; // Lấy tên file từ mảng tương ứng
        $quantity = filter_var($quantities[$i], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

        // Kiểm tra và chuyển ảnh vào thư mục uploaded_img
        $image_tmp_name = $_FILES['image']['tmp_name'][$i];
        $image_folder = '../uploaded_img/' . $image;
        move_uploaded_file($image_tmp_name, $image_folder);

        // Kiểm tra nếu sản phẩm đã tồn tại
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
        $select_products->execute([$name]);

        if ($select_products->rowCount() > 0) {
            $message[] = 'Tên sản phẩm ' . $name . ' đã tồn tại!';
        } else {
            // Thực hiện chèn vào cơ sở dữ liệu
            $insert_product = $conn->prepare("INSERT INTO `products` (name, category, price, image, quantity) VALUES (?, ?, ?, ?, ?)");
            $insert_product->execute([$name, $category, $price, $image, $quantity]);
            $message[] = 'Thêm sản phẩm ' . $name . ' thành công!';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('../uploaded_img/' . $fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);
    header('location:products.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php' ?>
    <style>
        /* Thêm margin giữa các form */
        .product {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
    <section class="add-products">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>Thêm sản phẩm</h3>
            <div id="product-container">
                <!-- Đây là container cho sản phẩm đầu tiên -->
                <div class="product">
                    <input type="text" required placeholder="Tên sản phẩm" name="name[]" maxlength="100" class="box">
                    <input type="number" min="0" max="9999999999" required placeholder="Giá" name="price[]"
                        onkeypress="if(this.value.length == 10) return false;" class="box">
                    <select name="category[]" class="box" required>
                        <option value="" disabled selected>Danh mục --</option>
                        <?php
                        $show_categories = $conn->prepare("SELECT * FROM `categories`");
                        $show_categories->execute();
                        while ($fetch_category = $show_categories->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $fetch_category['name'] . '">' . $fetch_category['name'] . '</option>';
                        }
                        ?>
                    </select>
                    <input type="file" name="image[]" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
                    <input type="number" min="1" required placeholder="Số lượng" name="quantity[]" class="box" value="1">
                </div>
            </div>
            

            <!-- Nút để thêm sản phẩm mới -->
            <button type="button" id="add-product-btn">Thêm Sản Phẩm Mới</button>

            <?php
            if (!empty($message)) {
                echo '<div class="message-box">';
                foreach ($message as $msg) {
                    echo '<p>' . $msg . '</p>';
                }
                echo '</div>';
            }
            ?>

            <input type="submit" value="Lưu" name="add_product" class="btn">
        </form>
    </section>

    <section class="show-products" style="padding-top: 0;">
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
                        <div class="quantity">Số lượng: <?= $fetch_products['quantity']; ?></div>
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
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addProductBtn = document.getElementById('add-product-btn');
            const productContainer = document.getElementById('product-container');

            // Bắt sự kiện click nút "Thêm Sản Phẩm Mới"
            addProductBtn.addEventListener('click', function () {
                // Clone sản phẩm đầu tiên và thêm vào container
                const firstProduct = document.querySelector('.product');
                const newProduct = firstProduct.cloneNode(true);
                productContainer.appendChild(newProduct);

                // Xóa nội dung input cho sản phẩm mới
                newProduct.querySelectorAll('input').forEach(input => input.value = '');

                // Add a delete button for the new product form
                const deleteBtn = document.createElement('button');
                deleteBtn.innerText = 'Xóa';
                deleteBtn.type = 'button';
                deleteBtn.classList.add('delete-btn');
                deleteBtn.addEventListener('click', function () {
                    productContainer.removeChild(newProduct);
                });

                newProduct.appendChild(deleteBtn);

                // Kiểm tra nếu chỉ có một form, thêm nút xóa cho form đầu tiên
                const productForms = document.querySelectorAll('.product');
                if (productForms.length === 1) {
                    const deleteBtnFirst = document.createElement('button');
                    deleteBtnFirst.innerText = 'Xóa';
                    deleteBtnFirst.type = 'button';
                    deleteBtnFirst.classList.add('delete-btn');
                    deleteBtnFirst.addEventListener('click', function () {
                        productContainer.removeChild(firstProduct);
                    });

                    firstProduct.appendChild(deleteBtnFirst);
                }
            });

            // Bổ sung nút xóa cho các form đã có
            productContainer.addEventListener('click', function (event) {
                if (event.target.classList.contains('delete-btn')) {
                    const productForm = event.target.closest('.product');
                    productContainer.removeChild(productForm);
                }
            });
        });
    </script>
</body>
</html>
