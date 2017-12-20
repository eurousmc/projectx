<?php
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

if (isset($_POST['project_id']) && is_numeric($_POST['project_id'])) $project_id = $_POST['project_id'];
else header('location: ../permission/login.php');

$sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {

  // check if project is not finish
  if ($row['Result'] != "-") header("location: ../permission/login.php");

  // find project name
  $project_name = $row['Projectname'];
}

// what is 2day
$date = date('Y-m-d');

// LOOP for get data and update tb_activity
for ($i = 0; $i < count($_POST['new_activity_name']); $i++) {

  // set activity para *********************************************************
  $activity_name = $_POST['new_activity_name'][$i];
  //$activity_startdate = $_POST['new_activity_startdate'][$i];
  $activity_enddate = $_POST['new_activity_enddate'][$i];
  $activity_staffname = $_POST['new_activity_staffname'][$i];
  $activity_status = $_POST['new_activity_status'][$i];

  if ($_SESSION['user_login_name'] == $activity_staffname) {
    $sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id AND Username = '$activity_staffname' AND Activity_id = ".($i+1).")";
    $rs = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($rs)) {
      if (($row['Status'] != $activity_status) && ($row['Status'] != "Finish")) {

        // update activity status ******************************************************
        if ($activity_status == "Finish") {
          if ($activity_enddate >= $date) $activity_result = "Successfully";
          else $activity_result = "Failed";
          $sql = "UPDATE tb_activity
          SET Status = '$activity_status', Realenddate = '$date', Result = '$activity_result'
          WHERE (Activity_id = ".($i+1)." AND Project_id = $project_id)";
        } else {
          $sql = "UPDATE tb_activity
          SET Status = '$activity_status'
          WHERE (Activity_id = ".($i+1)." AND Project_id = $project_id)";
        }
        mysqli_query($con, $sql);

        // set data to tb_notification ***********************************************
        if ($activity_status == "Wait") {
          $activity_status_msg = "รอดำเนินการ";
        } elseif ($activity_status == "Process") {
          $activity_status_msg = "กำลังดำเนินการ";
        } else {
          $activity_status_msg = "ดำเนินการแล้ว";
        }

        // add data to tb_notification for staff
        $sql = "SELECT DISTINCT(Username) FROM tb_activity WHERE (Project_id = $project_id)";
        $rs_notification = mysqli_query($con, $sql);
        while ($row_notification = mysqli_fetch_array($rs_notification)) {
          if ($_SESSION['user_login_name'] != $row_notification['Username']) {
            $sql = "INSERT INTO tb_notification
            VALUES(NULL,
              '{$row_notification['Username']}',
              '{$_SESSION['user_login_name']}',
              '{$_SESSION['user_login_realname']} ได้แก้ไขสถานะกิจกรรม $activity_name ใน $project_name เป็น $activity_status_msg',
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
              '{$_SESSION['user_login_realname']} ได้แก้ไขสถานะกิจกรรม $activity_name ใน $project_name เป็น $activity_status_msg',
              '$project_id',
              '".date('Y-m-d')."',
              '".date('H:i')."',
              'Unread')";
              mysqli_query($con, $sql);
          }

        }// close if ($row['Status'] != $activity_status) {
      }// close while ($row = mysqli_fetch_array($rs)) {

  } // close if ($_SESSION['user_login_name'] == $activity_staffname) {

} // close loop for $i *********************************************************

// check project is finished ***************************************************
$sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);

// 1 = finished
// 0 = inprocess
$project_success = 1;
while ($row = mysqli_fetch_array($rs)) {
  if ($row['Status'] != "Finish") $project_success = 0;
}
if ($project_success == 1) {

  // add data to tb_project
  $sql = "SELECT Enddate FROM tb_project WHERE (Project_id = $project_id)";
  $rs = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($rs)) {
    if ($row[0] >= $date) $project_result = "Successfully";
    else $project_result = "Failed";
  }
  $sql = "UPDATE tb_project
  SET Realenddate = '$date', Result = '$project_result'
  WHERE (Project_id = $project_id)";
  mysqli_query($con, $sql);

  // add data to tb_notification for staff
  $sql = "SELECT DISTINCT(Username) FROM tb_activity WHERE (Project_id = $project_id)";
  $rs = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($rs)) {
    $sql = "INSERT INTO tb_notification
    VALUES(NULL,
      '{$row['Username']}',
      '{$_SESSION['user_login_name']}',
      '$project_name เสร็จแล้ว',
      '$project_id',
      '".date('Y-m-d')."',
      '".date('H:i')."',
      'Unread')";
      mysqli_query($con, $sql);
  }

  // add data to tb_notification for manager
  $sql = "SELECT * FROM tb_user WHERE (Position = 'Manager')";
  $rs = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($rs)) {
    $sql = "INSERT INTO tb_notification
    VALUES(NULL,
      '{$row['Username']}',
      '{$_SESSION['user_login_name']}',
      '$project_name เสร็จแล้ว',
      '$project_id',
      '".date('Y-m-d')."',
      '".date('H:i')."',
      'Unread')";
      mysqli_query($con, $sql);
  }

}
//******************************************************************************

header("location: ../project_overview.php?project_id=$project_id&msg=แก้ไขสถานะกิจกรรมเรียบร้อย&page={$_GET['page']}");
