<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit;
}

include "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: products.php");
    exit;
}

$id = (int)$_GET['id'];

$query = mysqli_query($conn,"SELECT * FROM products WHERE id='$id'");

if(mysqli_num_rows($query)==0){
    header("Location: products.php");
    exit;
}

$product = mysqli_fetch_assoc($query);

if(isset($_POST['delete'])){

    if($product['image'] != "" && file_exists("../uploads/products/".$product['image'])){

        unlink("../uploads/products/".$product['image']);

    }

    mysqli_query($conn,"DELETE FROM products WHERE id='$id'");

    header("Location: products.php?deleted=1");
    exit;
}

?>
