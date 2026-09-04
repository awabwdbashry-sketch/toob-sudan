<?php

session_start();


if(!isset($_SESSION['admin_id'])){

    header("Location: ../login.php");
    exit;

}


include "../includes/db.php";


if(!isset($_GET['id']) || !isset($_GET['status'])){

    header("Location: users.php");
    exit;

}


$id = (int)$_GET['id'];

$status = $_GET['status'];


// السماح فقط بالحالتين

if($status != "active" && $status != "blocked"){

    header("Location: users.php");
    exit;

}


// تحديث حالة العميل

mysqli_query($conn,"
UPDATE users

SET status='$status'

WHERE id='$id'

");


header("Location: users.php");

exit;

?>