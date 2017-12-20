<?php
session_start();

// connect to data base
require("../connect.php");

// for protect SQL Injection!!!
$username = mysqli_real_escape_string($con, $_POST['login_username']);
$password = mysqli_real_escape_string($con, $_POST['login_password']);

// check username and password
$sql = "SELECT *
        FROM tb_user
        WHERE (Username = '$username') AND (BINARY Password = '$password')";
$rs = mysqli_query($con, $sql);

if (mysqli_num_rows($rs) == 1) {
  while ($row = mysqli_fetch_array($rs)) {

    /*
    // add data to tb_log ******************************************************
    // find IP address
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    // set date
    $date = date('Y-m-d');
    // set time
    $time = date('H:i');
    $sql = "INSERT INTO tb_user_log VALUES (NULL, {$row[0]}, $ip, $date, $time)";
    mysqli_query($con, $sql);
    //**************************************************************************
    */

    // create new session for user
    $_SESSION['user_login_name'] = $row[0];
    $_SESSION['user_login_realname'] = $row[2];
    $_SESSION['user_login_position'] = $row[3];
    $_SESSION['user_login_picture'] = $row[4];

    // go to index
    header("location: ../index.php");
  }
} else {
  header("location: login.php?msg=ขออภัย ชื่อผู้ใช้หรือรหัสผ่านของคุณไม่ถูกต้อง");
}

// close connection from database
mysqli_close($con);
