<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: categories.php");
    exit;
}

$id = (int)$_GET['id'];

$category = mysqli_query($conn,"
SELECT *
FROM categories
WHERE id='$id'
");

if(mysqli_num_rows($category)==0){
    header("Location: categories.php");
    exit;
}

$category = mysqli_fetch_assoc($category);

$count = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM products
WHERE category_id='$id'
");

$count = mysqli_fetch_assoc($count);

if($count['total'] > 0){

    echo "<script>
    alert('لا يمكن حذف هذا التصنيف لأنه يحتوي على منتجات.');
    window.location='categories.php';
    </script>";

    exit;

}

if($category['image'] != "" && file_exists("../uploads/categories/".$category['image'])){

    unlink("../uploads/categories/".$category['image']);

}

mysqli_query($conn,"
DELETE FROM categories
WHERE id='$id'
");

header("Location: categories.php");
exit;

?>