<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . "/db.php";

$result = mysqli_query($conn,"SELECT store_status, close_message FROM settings LIMIT 1");
$settings = mysqli_fetch_assoc($result);

$is_admin = isset($_SESSION['admin_id']);

if(
    !$is_admin &&
    isset($settings['store_status']) &&
    $settings['store_status'] == "closed"
){

    $message = $settings['close_message'];

    include __DIR__ . "/store_closed.php";

    exit;

}