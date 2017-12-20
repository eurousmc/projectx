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
function call_delete(file_id, filename, project_id, activity_id) {
  $(document).ready(function(){
    if (confirm("คุณต้องการลบเอกสาร " + filename + " ใช่หรือไม่?")) {
      $.ajax({
        url: "../delete/check_delete_fileupload_activity.php",
        type: "post",
        data: {"file_id": file_id, "project_id": project_id, "activity_id": activity_id},
        success: function (response) {
          $("#file_table_" + activity_id).html(response);
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
<!-- ######################################################################################################### -->

<div class="container">
  <h3>แก้ไขเอกสารเกี่ยวกับกิจกรรม</h3>

  <div class="container">

      <?php
      // loop for get data for tb_activity and print to javascript
      $sql_activity = "SELECT * FROM tb_activity WHERE (Project_id = $project_id)";
      $rs_activity = mysqli_query($con, $sql_activity);
      while ($row_activity = mysqli_fetch_array($rs_activity)) {
      ?>
      <form enctype="multipart/form-data" method="post" action="check_edit_fileupload_activity.php?page=<?= $page ?>">
        <input type="hidden" value="<?= $project_id ?>" name="project_id">
        <input type="hidden" value="<?= $row_activity[1] ?>" name="activity_id">

        <div class="container" id="activity_<?php echo $row_activity[1]; ?>">
          <h4>กิจกรรมที่&nbsp;<?php echo $row_activity[1]; ?></h4>
          <div class="container">

          <div class="form-group">
            <label>ชื่อกิจกรรม:&nbsp;</label>
            <?php echo $row_activity['Activityname']; ?>
          </div>

          <div class="form-group">
            <label>วันที่เริ่มกิจกรรม:&nbsp;</label>
            <?php echo $row_activity[4] ?>&nbsp;<b>ถึง</b>&nbsp;<?php echo $row_activity[5] ?>
          </div>

          <div class="form-group">
            <label>ผู้รับผิดชอบกิจกรรม:&nbsp;</label>
            <?php $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_activity['Username']}')";
            $userprofile_rs = mysqli_query($con, $sql);
            while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
              echo "<a href=\"../userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a>";
            } ?>
          </div>

          <div class="form-group">
            <label>สถานะ:</label>
            <?php if ($row_activity['Status'] == "Wait") echo "รอดำเนินการ";
            elseif ($row_activity['Status'] == "Process") echo "กำลังดำเนินการ";
            elseif ($row_activity['Status'] == "Finish") echo "ดำเนินการแล้ว"; ?>
          </div>

          <!-- show and edit file upload -->
          <!-- ######################################################################################################### -->

          <?php
          // to check if have no file in project
          $sql = "SELECT COUNT(File_id)
          FROM tb_fileupload
          WHERE (Project_id = $project_id AND Activity_id = {$row_activity[1]})";
          $rs_num = mysqli_query($con, $sql);
          while ($row_num = mysqli_fetch_array($rs_num))
          if ($row_num[0] > 0) {
            ?>
            <label>เอกสารเกี่ยวกับกิจกรรม:</label>
            <div class="table-responsive">
              <p id="file_table_<?= $row_activity[1] ?>">
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
                    WHERE (Project_id = $project_id AND Activity_id = {$row_activity[1]})
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
                            <button type=\"button\" class=\"btn btn-default btn-sm\" onclick=\"call_delete('{$row_fileupload['File_id']}', '{$row_fileupload['Filename']}', '$project_id', '{$row_activity[1]}');\">
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

          <?php } ?>

          <?php
          $sql = "SELECT Username FROM tb_project WHERE (Project_id = $project_id)";
          $rs = mysqli_query($con, $sql);
          while ($row = mysqli_fetch_array($rs)) {
            $username_permission = $row['Username'];
          }
          if (($username_permission == $_SESSION['user_login_name']) ||
            (($row_activity['Username'] == $_SESSION['user_login_name']) && ($row_activity['Status'] != "Finish"))) {
          ?>

          <div class="form-group">
            <label for="new_activity_file">เพิ่มเอกสารเกี่ยวกับกิจกรรม:</label>
            <input type="file" id="new_activity_file" name="new_activity_file[]" multiple>
            <small class="form-text text-muted">เอกสารจะต้องมีขนาดไม่เกิน 8 MB. ต่อหนึ่งเอกสาร และสามารถเพิ่มได้มากกว่าหนึ่งเอกสาร</small>
          </div>
          <button type="submit" class="btn btn-default"><span class="glyphicon">&#xe172;</span>&nbsp;บันทึก</button>
        <?php } ?>

        </div>
      </div>
      <hr>
    </form>
<?php
  } // close while ($row_activity = mysqli_fetch_array($rs_activity)) {
?>
</div> <!-- close container -->
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
