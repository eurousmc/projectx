<?php
// Check user login
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

$username = $_SESSION['user_login_name'];

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
    move_uploaded_file($_FILES["new_login_picture"]["tmp_name"], "../userpicture/$username.$type");
    $filename = "$username.$type";

    // add filename to database
    $sql = "UPDATE tb_user
    SET Filename = '$filename'
    WHERE (Username = '$username')";
    if (mysqli_query($con, $sql)) {
      header("location: ../userprofile.php?username=$username&msg=แก้ไขรูปภาพประจำตัวเรียบร้อย");
    } else {
      header("location: ../userprofile.php?username=$username&msg=ไม่สามารถแก้ไขรูปภาพประจำตัวได้ในขนะนี้ โปรดลองอีกครั้ง");
    }
  } else {
    header("location: edit_userpicture.php?msg=โปรดเลือกชนิดของรูปภาพตามที่กำหนด");
  }

} else {
  header("location: edit_userpicture.php?msg=โปรดเลือกรูปภาพก่อนทำการบันทึก");
}
// end upload's code ****************************************************************

// close connection from database
mysqli_close($con);
