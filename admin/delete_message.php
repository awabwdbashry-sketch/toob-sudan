<?php

session_start();


if(!isset($_SESSION['admin_id'])){

    header("Location: ../login.php");
    exit;

}


include "../includes/db.php";


if(!isset($_GET['id'])){

    header("Location: messages.php");
    exit;

}


$id = (int)$_GET['id'];


// حذف الرسالة

mysqli_query($conn,"
DELETE FROM contact_messages
WHERE id='$id'
");


header("Location: messages.php");

exit;

?>