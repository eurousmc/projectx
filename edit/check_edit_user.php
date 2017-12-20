<?php
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Administrator")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

$page = $_POST['page'];

$username = $_SESSION['$new_username'];
$password = $_SESSION['$new_password'] = $_POST['new_login_password'];
$realname = $_SESSION['$new_realname'] = $_POST['new_login_realname'];
$position = $_SESSION['$new_position'] = $_POST['new_login_position'];

// upload's code here ****************************************************************

if (!empty($_FILES['new_login_picture']['name'])) {

  //get file type
  $type = strtolower(pathinfo($_FILES['new_login_picture']['name'], PATHINFO_EXTENSION));
  // check file type
  if ($type == "jpg" || $type == "jpeg" || $type == "png") {
    // remove old file!
    $sql = "SELECT Filename FROM tb_user WHERE (Username = '$username')";
    $rs = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($rs)) {
      unlink("../userpicture/{$row[0]}");
    }
    // uploading...
    copy($_FILES["new_login_picture"]["tmp_name"], "../userpicture/$username.$type");
    $filename = "$username.$type";

    // add filename to database
    $sql = "UPDATE tb_user
    SET Password = '$password', Realname = '$realname', Position = '$position', Filename = '$filename'
    WHERE (Username = '$username')";
  } else {
    $sql = "UPDATE tb_user
    SET Password = '$password', Realname = '$realname', Position = '$position'
    WHERE (Username = '$username')";
  }

} else {
  $sql = "UPDATE tb_user
  SET Password = '$password', Realname = '$realname', Position = '$position'
  WHERE (Username = '$username')";
}
// end upload's code ****************************************************************



if (mysqli_query($con, $sql)) {
  header("location: ../manage_user.php?page=$page&msg=แก้ไขข้อมูล $username เรียบร้อย");
} else {
  header("location: edit_user.php?page=$page&msg=ไม่สามารถแก้ไขข้อมูลได้ในขนะนี้ โปรดลองอีกครั้ง");
}

// close connection from database
mysqli_close($con);
