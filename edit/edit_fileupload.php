<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) $project_id = $_GET['project_id'];
else header('location: ../permission/login.php');

$page = $_GET["page"];

// check if project is not finish
$sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  if ($row['Result'] != "-") header("location: ../permission/login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">

  <link rel="icon" href="../systempicture/page_icon.ico">

  <title>ระบบวิเคราะห์และติดตามงานพัฒนาซอฟต์แวร์</title>

  <!-- jQuery core JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <!-- Bootstrap Date-Picker Plugin -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"/>

  <!-- java script -->
  <script>
// call to delete fileupload
function call_delete(file_id, filename, project_id) {
  $(document).ready(function(){
    if (confirm("คุณต้องการลบเอกสาร " + filename + " ใช่หรือไม่?")) {
      $.ajax({
        url: "../delete/check_delete_fileupload.php",
        type: "post",
        data: {"file_id": file_id, "project_id": project_id},
        success: function (response) {
          $("#file_table").html(response);
          alert("เอกสาร " + filename + " ถูกลบแล้ว");
        }
      });
    }
  });
}
</script>

<!-- Script for notification -->
<script>
$(document).ready(function(){

  function Load_notification() {
    $.ajax({
      url: "../badges/message_badges.php",
      success: function (response) {
        $("#message_badges").html(response);
      }
    });
    $.ajax({
      url: "../badges/notification_badges.php",
      success: function (response) {
        $("#notification_badges").html(response);
      }
    });
  }

  setInterval(function() {
    Load_notification()
  }, 100);

});
</script>

<body>
  <div class="container"><h1>ระบบวิเคราะห์และติดตามงานพัฒนาซอฟต์แวร์</h1></div>
  <nav class="navbar navbar-default">
    <div class="container-fluid">

      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div>

      <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li><a href="../index.php"><span class="glyphicon">&#xe021;</span>&nbsp;หน้าแรก</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="../userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="../userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
        <li><a href="../conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <li><a href="../search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
        <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <div class="container">
    <form enctype="multipart/form-data" method="post" action="check_edit_fileupload.php?page=<?= $page ?>">
      <input type="hidden" value="<?= $project_id ?>" name="project_id">
      <input type="hidden" value="0" name="activity_id">
      <div class="form-group">
        <label for="new_project_file">เพิ่มเอกสารเกี่ยวกับโครงการ:</label>
          <input type="file" id="new_project_file" name="new_project_file[]" multiple>
          <small class="form-text text-muted">เอกสารจะต้องมีขนาดไม่เกิน 8 MB. ต่อหนึ่งเอกสาร และสามารถเพิ่มได้มากกว่าหนึ่งเอกสาร</small>
      </div>
      <button type="submit" class="btn btn-default"><span class="glyphicon">&#xe172;</span>&nbsp;บันทึก</button>
      </form>
      <hr>
<!-- show and edit file upload -->
<!-- ######################################################################################################### -->

<?php
// to check if have no file in project
$sql = "SELECT COUNT(File_id)
FROM tb_fileupload
WHERE (Project_id = $project_id AND Activity_id = 0)";
$rs_num = mysqli_query($con, $sql);
while ($row_num = mysqli_fetch_array($rs_num))
if ($row_num[0] > 0) {
?>

      <div class="table-responsive">
      <p id="file_table">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ชื่อเอกสาร</th>
              <th>เพิ่มโดย</th>
              <th>ลบ</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql_fileupload = "SELECT *
            FROM tb_fileupload
            WHERE (Project_id = $project_id AND Activity_id = 0)
            ORDER BY File_id DESC";
            $rs_fileupload = mysqli_query($con, $sql_fileupload);
            while ($row_fileupload = mysqli_fetch_array($rs_fileupload)) { ?>
            <tr>
              <td>
              <a href="../fileupload/<?= $row_fileupload['Filename'] ?>"><?php echo $row_fileupload['Filename']; ?></a>
              </td>
              <td>
              <?php
              $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_fileupload['Username']}')";
              $userprofile_rs = mysqli_query($con, $sql);
              while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
                if ($row_fileupload['Username'] == $_SESSION['user_login_name'])
                  echo "<a href=\"../userprofile.php?username={$userprofile_row[0]}\">คุณ</a>";
                else
                  echo "<a href=\"../userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a>";
              }
              ?>
              </td>
              <?php if ($row_fileupload['Username'] == $_SESSION['user_login_name']) {
                echo "<td>
                <button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"call_delete('{$row_fileupload['File_id']}', '{$row_fileupload['Filename']}', '$project_id');\">
                <span class=\"glyphicon glyphicon-trash\"></span>
                </button>
                </td>
                ";
              } else {
                echo "<td>&nbsp;</td>";
              } ?>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </p>
      </div>

<?php } else { ?>
<center><h4>ยังไม่มีเอกสารประกอบโครงการนี้</h4></center>
<?php } ?>
<!-- ######################################################################################################### -->
<center>
  <p>
    <a href="../project_overview.php?project_id=<?= $project_id ?>&page=<?= $page ?>" class="btn btn-default" role="button">กลับ</a>
  </p>
</center>

  </div> <!-- close main container -->
</body>
</html>

<?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>
