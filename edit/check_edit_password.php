<?php
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

$username = $_SESSION['user_login_name'];
$old_password = $_POST['old_login_password'];
$new_password = $_POST['new_login_password'];
$confirm_password = $_POST['new_confirm_login_password'];

$sql = "SELECT * FROM tb_user WHERE ((Username = '$username') AND (BINARY Password = '$old_password'))";
$rs = mysqli_query($con, $sql);
if (mysqli_num_rows($rs) == 1) {
  $sql = "UPDATE tb_user SET Password='$new_password' WHERE(Username='$username')";
  if (mysqli_query($con, $sql)) {
    header("location: ../userprofile.php?username={$_SESSION['user_login_name']}&msg=บันทึกรหัสผ่านใหม่เรียบร้อย");
  } else {
    header("location: edit_password.php?msg=ไม่สามารถบันทึกรหัสผ่านได้ในขนะนี้ โปรดลองอีกครั้ง");
  }
} else {
  header("location: edit_password.php?msg=รหัสผ่านเดิมไม่ถูกต้อง โปรดลองอีกครั้ง");
}

// close connection from database
mysqli_close($con);
