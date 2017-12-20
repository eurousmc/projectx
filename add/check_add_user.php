<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Administrator")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

$page = $_POST['page'];

// create session and set paramiter to user in this page
$new_username = $_SESSION['new_username'] = $_POST['new_login_username'];
$new_password = $_SESSION['new_password'] = $_POST['new_login_password'];
$new_confirm_password = $_SESSION['new_confirm_password'] = $_POST['new_confirm_login_password'];
$new_realname = $_SESSION['new_realname'] = $_POST['new_login_realname'];
$new_position = $_SESSION['new_position'] = $_POST['new_login_position'];

// check username if has already
$sql = "SELECT * FROM tb_user WHERE (Username = '$new_username')";
$rs = mysqli_query($con, $sql);
if (mysqli_num_rows($rs) == 0) {

  // upload's code here ****************************************************************
  if (!empty($_FILES['new_login_picture']['name'])) {
    //get file type
    $type = strtolower(pathinfo($_FILES['new_login_picture']['name'], PATHINFO_EXTENSION));
    // check file type
    if ($type == "jpg" || $type == "jpeg" || $type == "png") {
      // uploading...
      move_uploaded_file($_FILES["new_login_picture"]["tmp_name"], "../userpicture/$new_username.$type");
      $filename = "$new_username.$type";
    } else {
      copy("../systempicture/user_unknow.png", "../userpicture/$new_username.png");
      $filename = "$new_username.png";
    }
  } else {
    copy("../systempicture/user_unknow.png", "../userpicture/$new_username.png");
    $filename = "$new_username.png";
  }
  // end upload's code ****************************************************************

  // add data to tb_user
  $sql = "INSERT INTO tb_user VALUES('$new_username', '$new_password', '$new_realname', '$new_position', '$filename')";
  if (mysqli_query($con, $sql)) {
    // clesr session
    unset($_SESSION['new_username']);
    unset($_SESSION['new_password']);
    unset($_SESSION['new_confirm_password']);
    unset($_SESSION['new_realname']);
    unset($_SESSION['new_position']);

    header("location: ../manage_user.php?page=$page&msg=บันทึกเรียบร้อย");
  } else {
    header("location: add_user.php?page=$page&msg=ไม่สามารถบันทึกบัญชีได้ในขณะนี้ โปรดลองอีกครั้ง");
  }

} else {
  header("location: add_user.php?page=$page&msg=ไม่สามารถใช้ชื่อบัญชีนี้ได้ เนื่องจากมีบัญชีนี้อยู่ในระบบแล้ว โปรดลองใช้ชื่อบัญชีอื่น");
}

// close connection from database
mysqli_close($con);
