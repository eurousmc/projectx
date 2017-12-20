<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Administrator")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

$username = $_GET['username'];
$page = $_GET['page'];

// remove picture file!
$sql = "SELECT Filename FROM tb_user WHERE (Username = '$username')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  unlink("../userpicture/{$row[0]}");
}

// delete user from database
$sql = "DELETE FROM tb_user WHERE (Username = '$username')";
mysqli_query($con, $sql);

// close connection from database
mysqli_close($con);

header("location: ../manage_user.php?page=$page");
