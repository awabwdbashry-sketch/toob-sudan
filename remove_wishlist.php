<?php

session_start();
include 'includes/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['id'])){

$product_id = (int)$_GET['id'];

mysqli_query($conn,"
DELETE FROM wishlist
WHERE user_id='$user_id'
AND product_id='$product_id'
");

}

header("Location: ".$_SERVER['HTTP_REFERER']);
exit;

?>