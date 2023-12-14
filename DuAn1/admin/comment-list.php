<?php
session_start();
include '../components/connect.php';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bình luận</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php' ?>
    <div class="admin-comment">
        <table>
            <?php 
            include '../comment.php';
            if(!is_null(showCommentListAdmin())){
                ?>
                <tr>
                    <th>UserID</th>
                    <th>ProductID</th>
                    <th>Comment</th>
                    <th>Create at</th>
                    <th>Action</th>
                </tr>
                <?php //HTML
                foreach (showCommentListAdmin() as $comment) :
                    ?>
                    <tr>
                        <td><?= $comment['userId'] ?></td>
                        <td><?= $comment['productId'] ?></td>
                        <td><?= $comment['comment'] ?></td>
                        <td><?= $comment['created_at'] ?></td>
                        <td>
                            <a href="../comment.php?action=deleteComment&comment=<?= $comment["comment"] ?>&productId=<?= $comment['productId'] ?>&back=admin" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                    <?php //HTML
                endforeach;
            }else{
                echo "Chưa có bình luận nào";
            }
            ?>
        </table>
    </div>
</body>
</html>
