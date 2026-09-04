<?php

session_start();


if(!isset($_SESSION['admin_id'])){

    header("Location: ../login.php");
    exit;

}


include "../includes/db.php";


if(!isset($_GET['id'])){

    header("Location: reviews.php");
    exit;

}


$id = (int)$_GET['id'];


// حذف التقييم

mysqli_query($conn,"
DELETE FROM reviews
WHERE id='$id'
");


header("Location: reviews.php");

exit;

?>