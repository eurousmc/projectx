<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

// get project id
if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) $project_id = $_GET['project_id'];
else header('location: ../permission/login.php');

$sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  // check permission to delete project
  if ($row['Username'] != $_SESSION['user_login_name']) header("location: ../permission/login.php");

  //get project name
  $project_name = $row['Projectname'];
}

// -----------------------------------------------------------------------------

// add data to db_notification for staff
$sql = "SELECT DISTINCT(Username)
FROM tb_activity
WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  if ($_SESSION['user_login_name'] != $row['Username']) {
    $sql = "INSERT INTO tb_notification
    VALUES(NULL,
      '{$row['Username']}',
      '{$_SESSION['user_login_name']}',
      '{$_SESSION['user_login_realname']} ได้ลบ $project_name',
      '0',
      '".date('Y-m-d')."',
      '".date('H:i')."',
      'Unread')";
      mysqli_query($con, $sql);
  }
}

// add data to db_notification for manager
$sql = "SELECT * FROM tb_user WHERE (Position = 'Manager')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
$sql = "INSERT INTO tb_notification
VALUES(NULL,
  '{$row['Username']}',
  '{$_SESSION['user_login_name']}',
  '{$_SESSION['user_login_realname']} ได้ลบ $project_name',
  '0',
  '".date('Y-m-d')."',
  '".date('H:i')."',
  'Unread')";
  mysqli_query($con, $sql);
}
// -----------------------------------------------------------------------------

// delete project
$sql = "DELETE FROM tb_project WHERE (Project_id = '$project_id')";
mysqli_query($con, $sql);

// delete activity
$sql = "DELETE FROM tb_activity WHERE (Project_id = '$project_id')";
mysqli_query($con, $sql);

// delete file
$sql = "SELECT * FROM tb_fileupload WHERE (Project_id = '$project_id')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  unlink("../fileupload/{$row[1]}");
}

// delete file data
$sql = "DELETE FROM tb_fileupload WHERE (Project_id = '$project_id')";
mysqli_query($con, $sql);


// close connection from database
mysqli_close($con);

header("location: ../index.php?page={$_GET['page']}");
?>
