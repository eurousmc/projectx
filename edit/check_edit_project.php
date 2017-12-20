<?php
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

if (isset($_POST['project_id']) && is_numeric($_POST['project_id'])) $project_id = $_POST['project_id'];
else header('location: ../permission/login.php');

// set paramiter
$project_name = $_POST['new_project_name'];
$project_employer = $_POST['new_project_employer'];
$project_startdate = $_POST['new_project_startdate'];
$project_enddate = $_POST['new_project_enddate'];
$project_requirement = $_POST['new_project_requirement'];
$project_staff = $_POST['new_project_staff'];

// check if project is not finish
$sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  if ($row['Result'] != "-") header("location: ../permission/login.php");
}

// update project data to database
$sql = "UPDATE tb_project
SET Projectname = '$project_name', Employer = '$project_employer', Startdate = '$project_startdate', Enddate = '$project_enddate', Requirement = '$project_requirement'
WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);

// add data to tb_notification for manager
$sql = "SELECT * FROM tb_user WHERE (Position = 'Manager')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
$sql = "INSERT INTO tb_notification
VALUES(NULL,
  '{$row['Username']}',
  '{$_SESSION['user_login_name']}',
  '{$_SESSION['user_login_realname']} ได้แก้ไขข้อมูล $project_name กำหนดเวลาพัฒนา $project_startdate ถึง $project_enddate',
  '$project_id',
  '".date('Y-m-d')."',
  '".date('H:i')."',
  'Unread')";
  mysqli_query($con, $sql);
}

header("location: ../project_overview.php?project_id=$project_id&msg=แก้ไขข้อมูลโครงการ $project_name เรียบร้อย&page={$_GET['page']}");
?>
