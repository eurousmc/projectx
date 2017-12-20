<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

$file_id = $_POST['file_id'];
$project_id = $_POST['project_id'];
$activity_id = $_POST['activity_id'];

// check permission to delete project
$sql = "SELECT Username FROM tb_fileupload WHERE (File_id = '$file_id')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  if ($row[0] != $_SESSION['user_login_name']) header("location: ../permission/login.php");
}

// delete file
$sql = "SELECT * FROM tb_fileupload WHERE (File_id = '$file_id')";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  $userfile = $row['Filename'];
  unlink("../fileupload/$userfile");
}

// delete file data
$sql = "DELETE FROM tb_fileupload WHERE (File_id = '$file_id')";
mysqli_query($con, $sql);

//get project name
$sql = "SELECT Projectname FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  $project_name = $row['Projectname'];
}

// find Activityname name
$sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id AND Activity_id = $activity_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  $activity_name = $row['Activityname'];
}

//******************************************************************************
    // add data to db_notification for staff
    $sql = "SELECT DISTINCT(Username) FROM tb_activity WHERE (Project_id = $project_id)";
    $rs = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($rs)) {
      if ($_SESSION['user_login_name'] != $row['Username']) {
        $sql = "INSERT INTO db_notification
        VALUES(NULL,
          '{$row['Username']}',
          '{$_SESSION['user_login_name']}',
          '{$_SESSION['user_login_realname']} ได้ลบเอกสาร $userfile ออกจาก $activity_name ของ $project_name',
          '$project_id',
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
    $sql = "INSERT INTO db_notification
    VALUES(NULL,
      '{$row['Username']}',
      '{$_SESSION['user_login_name']}',
      '{$_SESSION['user_login_realname']} ได้ลบเอกสาร $userfile ออกจาก $activity_name ของ $project_name',
      '0',
      '".date('Y-m-d')."',
      '".date('H:i')."',
      'Unread')";
      mysqli_query($con, $sql);
    }
//******************************************************************************

// show data table
echo "<table class=\"table table-striped\">
<thead>
<tr>
<th>ชื่อเอกสาร</th>
<th>เพิ่มโดย</th>
<th>ลบ</th>";

echo "</tr>
</thead>
<tbody>";

$sql_fileupload = "SELECT *
FROM tb_fileupload
WHERE (Project_id = $project_id AND Activity_id = $activity_id)
ORDER BY File_id DESC";
$rs_fileupload = mysqli_query($con, $sql_fileupload);
while ($row_fileupload = mysqli_fetch_array($rs_fileupload)) {

  // print data
  //echo "<tr><td>{$row_fileupload['Filename']}</td>";
  echo "<tr><td><a href=\"../fileupload/{$row_fileupload['Filename']}\">{$row_fileupload['Filename']}</a></td>";

  $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_fileupload['Username']}')";
  $userprofile_rs = mysqli_query($con, $sql);
  while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
    if ($row_fileupload['Username'] == $_SESSION['user_login_name'])
      echo "<td><a href=\"../userprofile.php?username={$userprofile_row[0]}\">คุณ</a></td>";
    else
      echo "<td><a href=\"../userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a></td>";
  }

  if ($row_fileupload['Username'] == $_SESSION['user_login_name']) {
    echo "<td>
    <button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"call_delete('{$row_fileupload['File_id']}', '{$row_fileupload['Filename']}', '$project_id', '$activity_id');\">
    <span class=\"glyphicon glyphicon-trash\"></span>
    </button>
    </td>
    ";
  } else {
    echo "<td>&nbsp;</td>";
  }
  echo "</tr>";
}

echo "</tbody>
</table>";

// close connection from database
mysqli_close($con);
?>
