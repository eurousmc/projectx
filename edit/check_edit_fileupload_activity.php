<?php
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

if (isset($_POST['project_id']) && is_numeric($_POST['project_id'])) $project_id = $_POST['project_id'];
else header('location: ../permission/login.php');

$activity_id = $_POST['activity_id'];

$sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {

  // check if project is not finish
  if ($row['Result'] != "-") header("location: ../permission/login.php");

  // find project name
  $project_name = $row['Projectname'];
}

// find Activityname name
$sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id AND Activity_id = $activity_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  $activity_name = $row['Activityname'];
}

// upload's code here **********************************************************
//foreach ($_FILES["file1"]["tmp_name"] as $key=>$tmp_name) {
if (!empty($_FILES['new_activity_file']['name'][0])) {
  for ($key = 0; $key < count($_FILES["new_activity_file"]["name"]); $key++) {

    //print_r($_FILES["file1"]); echo "<br>";
    $userfile = $_FILES["new_activity_file"]["name"][$key];
    $user_upload = $_SESSION['user_login_name'];

    // change file name if found it on server
    for (;;) {
      $sql = "SELECT * FROM tb_fileupload WHERE(Filename='$userfile')";
      $rs = mysqli_query($con, $sql);
      if (mysqli_num_rows($rs) != 0) {
        $userfile = rand(0, 9).$userfile;
      } else {
        break;
      }
    }

    // uploading...
    move_uploaded_file($_FILES["new_activity_file"]["tmp_name"][$key], "../fileupload/$userfile");

    // add data to data base
    $sql = "INSERT INTO tb_fileupload VALUES(NULL, '$userfile', '".date('Y-m-d')."', '$user_upload', $project_id, $activity_id)";
    mysqli_query($con, $sql);

//******************************************************************************
    // add data to tb_notification for staff
    $sql = "SELECT DISTINCT(Username) FROM tb_activity WHERE (Project_id = $project_id)";
    $rs = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($rs)) {
      if ($_SESSION['user_login_name'] != $row['Username']) {
        $sql = "INSERT INTO tb_notification
        VALUES(NULL,
          '{$row['Username']}',
          '{$_SESSION['user_login_name']}',
          '{$_SESSION['user_login_realname']} ได้เพิ่มเอกสาร $userfile ลงใน $activity_name ของ $project_name',
          '$project_id',
          '".date('Y-m-d')."',
          '".date('H:i')."',
          'Unread')";
          mysqli_query($con, $sql);
      }
    }

    // add data to tb_notification for manager
    $sql = "SELECT * FROM tb_user WHERE (Position = 'Manager')";
    $rs = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($rs)) {
      $sql = "INSERT INTO tb_notification
      VALUES(NULL,
        '{$row['Username']}',
        '{$_SESSION['user_login_name']}',
        '{$_SESSION['user_login_realname']} ได้เพิ่มเอกสาร $userfile ลงใน $activity_name ของ $project_name',
        '$project_id',
        '".date('Y-m-d')."',
        '".date('H:i')."',
        'Unread')";
        mysqli_query($con, $sql);
    }
//******************************************************************************

  }

  header("location: edit_fileupload_activity.php?project_id=$project_id&msg=เพิ่มเอกสารเรียบร้อย&page={$_GET['page']}");
} else {
  header("location: edit_fileupload_activity.php?project_id=$project_id&msg=โปรดเลือกเอกสาร&page={$_GET['page']}");
}
// end upload's code ****************************************************************
