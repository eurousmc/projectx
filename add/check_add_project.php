<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

// set projectID
$sql = "SELECT MAX(Project_id) FROM tb_project";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  if ($row == null or $row == "") $project_id = 1;
  else $project_id = $row[0] + 1;
}

// set project para
$project_name = $_POST['new_project_name'];
$project_employer = $_POST['new_project_employer'];
$project_startdate = $_POST['new_project_startdate'];
$project_enddate = $_POST['new_project_enddate'];
$project_requirement = $_POST['new_project_requirement'];
if ($project_requirement == "" || $project_requirement == null) $project_requirement = "-";
$user_upload = $_SESSION['user_login_name'];

// add project data
$sql = "INSERT INTO tb_project
        VALUES($project_id,
                '$project_name',
                '$project_employer',
                '$project_startdate',
                '$project_enddate',
                '$project_requirement',
                '{$_SESSION['user_login_name']}',
                '-',
                '-'
              )";
mysqli_query($con, $sql);

// upload's code here ****************************************************************
//foreach ($_FILES["file1"]["tmp_name"] as $key=>$tmp_name) {
  if (!empty($_FILES['new_project_file']['name'][0])) {
    for ($key = 0; $key < count($_FILES["new_project_file"]["name"]); $key++) {

      //print_r($_FILES["file1"]); echo "<br>";
      $userfile = $_FILES["new_project_file"]["name"][$key];

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
      move_uploaded_file($_FILES["new_project_file"]["tmp_name"][$key], "../fileupload/$userfile");

      // add data to data base
      $sql = "INSERT INTO tb_fileupload VALUES(NULL, '$userfile', '".date('Y-m-d')."', '$user_upload', $project_id, 0)";
      mysqli_query($con, $sql);
    }
  }
  // end upload's code ****************************************************************

// add data to tb_notification for manager
$sql = "SELECT * FROM tb_user WHERE (Position = 'Manager')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
$sql = "INSERT INTO tb_notification
VALUES(NULL,
  '{$row['Username']}',
  '{$_SESSION['user_login_name']}',
  '{$_SESSION['user_login_realname']} ได้สร้าง $project_name กำหนดเวลาพัฒนา $project_startdate ถึง $project_enddate',
  '$project_id',
  '".date('Y-m-d')."',
  '".date('H:i')."',
  'Unread')";
  mysqli_query($con, $sql);
}

// add activity data
for ($i = 0; $i < count($_POST['new_activity_name']); $i++) {

  // set activity para
  $activity_name = $_POST['new_activity_name'][$i];
  $activity_startdate = $_POST['new_activity_startdate'][$i];
  $activity_enddate = $_POST['new_activity_enddate'][$i];
  $activity_staffname = $_POST['new_activity_staffname'][$i];
  //$activity_status = $_POST['new_activity_status'][$i];

  // add to tb_activity
  $sql = "INSERT INTO tb_activity
  VALUES(
    $project_id,
    ($i+1),
    '$activity_name',
    '$activity_staffname',
    '$activity_startdate',
    '$activity_enddate',
    'Wait',
    '-',
    '-'
  )";
  mysqli_query($con, $sql);

  // upload's code here ****************************************************************
  //foreach ($_FILES["file1"]["tmp_name"] as $key=>$tmp_name) {
    if (!empty($_FILES["new_activity_file_$i"]["name"][0])) {
      for ($key = 0; $key < count($_FILES["new_activity_file_$i"]["name"]); $key++) {

        //print_r($_FILES["file1"]); echo "<br>";
        $userfile = $_FILES["new_activity_file_$i"]["name"][$key];

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
        move_uploaded_file($_FILES["new_activity_file_$i"]["tmp_name"][$key], "../fileupload/$userfile");

        // add data to data base
        $sql = "INSERT INTO tb_fileupload VALUES(NULL, '$userfile', '".date('Y-m-d')."', '$user_upload', $project_id, $i)";
        mysqli_query($con, $sql);
      }
    }
    // end upload's code ****************************************************************

  // add data to tb_notification for staff
  if ($_SESSION['user_login_name'] != $activity_staffname) {
    $sql = "INSERT INTO tb_notification
    VALUES(NULL,
      '$activity_staffname',
      '{$_SESSION['user_login_name']}',
      '{$_SESSION['user_login_realname']} ได้สร้าง $project_name และกำหนดให้คุณเป็นผู้รับผิดชอบกิจกรรม $activity_name ระยะเวลาของกิจกรรมคือ $activity_startdate ถึง $activity_enddate',
      '$project_id',
      '".date('Y-m-d')."',
      '".date('H:i')."',
      'Unread')";
      mysqli_query($con, $sql);
  }
}

// close connection from database
mysqli_close($con);

header("location: ../index.php?msg=สร้างโครงการเรียบร้อย");
