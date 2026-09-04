<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";


if(!isset($_GET['id'])){
    header("Location: orders.php");
    exit;
}


$id = (int)$_GET['id'];


// حذف تفاصيل المنتجات أولاً
mysqli_query($conn,"
DELETE FROM order_items
WHERE order_id='$id'
");


// حذف الطلب
mysqli_query($conn,"
DELETE FROM orders
WHERE id='$id'
");


header("Location: orders.php");
exit;

?>