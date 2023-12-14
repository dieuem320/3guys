<?php
include '../components/connect.php';

// Phần đầu tiên của header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <title>Danh mục</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .add-categories,
    .show-categories,
    .update-categories,
    .delete-categories {
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    h3 {
        font-size: 1.5em;
        margin-bottom: 10px;
    }

    .box {
        margin-bottom: 10px;
        padding: 8px;
    }

    .btn {
        padding: 10px;
        background-color: #3498db;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #2980b9;
    }

    .box-container {
        display: flex;
        flex-wrap: wrap;
    }

    .box {
        width: calc(33.33% - 20px);
        margin: 10px;
        padding: 15px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .name {
        font-size: 1.2em;
        margin-bottom: 10px;
    }

    .empty {
        margin-top: 20px;
    }
</style>
<body>
<?php include '../components/admin_header.php' ?>

<section class="add-categories">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>Thêm danh mục</h3>
        <input type="text" required placeholder="Tên danh mục" name="category_name" maxlength="255" class="box">
        <input type="submit" value="Thêm" name="add_category" class="btn">
    </form>
</section>

<?php
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $category_name = filter_var($category_name, FILTER_SANITIZE_STRING);

    $insert_category = $conn->prepare("INSERT INTO `categories` (name) VALUES (?)");
    $insert_category->execute([$category_name]);

    $message[] = 'Thêm danh mục thành công!';
}

?>

<section class="show-categories">
    <div class="box-container">
        <?php
        $show_categories = $conn->prepare("SELECT * FROM `categories`");
        $show_categories->execute();
        if ($show_categories->rowCount() > 0) {
            while ($fetch_category = $show_categories->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="box">
                    <div class="name"><?= $fetch_category['name']; ?></div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">Chưa có danh mục!</p>';
        }
        ?>
    </div>
</section>

<section class="update-categories">
    <form action="" method="POST">
        <h3>Sửa danh mục</h3>
        <select name="selected_category" class="box" required>
            <option value="" disabled selected>Chọn danh mục</option>
            <?php
            $show_categories = $conn->prepare("SELECT * FROM `categories`");
            $show_categories->execute();
            while ($fetch_category = $show_categories->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $fetch_category['id'] . '">' . $fetch_category['name'] . '</option>';
            }
            ?>
        </select>
        <input type="text" required placeholder="Tên danh mục mới" name="new_category_name" maxlength="255"
               class="box">
        <input type="submit" value="Sửa" name="update_category" class="btn">
    </form>
</section>

<section class="delete-categories">
    <form action="" method="POST">
        <h3>Xóa danh mục</h3>
        <select name="selected_category_delete" class="box" required>
            <option value="" disabled selected>Chọn danh mục</option>
            <?php
            $show_categories = $conn->prepare("SELECT * FROM `categories`");
            $show_categories->execute();
            while ($fetch_category = $show_categories->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $fetch_category['id'] . '">' . $fetch_category['name'] . '</option>';
            }
            ?>
        </select>
        <input type="submit" value="Xóa" name="delete_category" class="btn">
    </form>
</section>

</body>
</html>
