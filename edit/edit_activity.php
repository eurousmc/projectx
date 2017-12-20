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

</head>
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
    <h3>แก้ไขสถานะกิจกรรม</h3>

    <form method="post" action="check_edit_activity.php?page=<?= $page ?>">

      <input type="hidden" value="<?= $project_id ?>" name="project_id">

      <?php
      // loop for get data for tb_activity and print to javascript
      $sql_activity = "SELECT * FROM tb_activity WHERE (Project_id = $project_id)";
      $rs_activity = mysqli_query($con, $sql_activity);
      while ($row_activity = mysqli_fetch_array($rs_activity)) {
        ?>

<!-- ######################################################################################################### -->

        <div class="container" id="activity_<?php echo $row_activity[1]; ?>">
          <h4>กิจกรรมที่&nbsp;<?php echo $row_activity[1]; ?></h4>
          <div class="container">

          <div class="form-group">
            <label>ชื่อกิจกรรม:&nbsp;</label>
            <?php echo $row_activity['Activityname']; ?>
            <input type="hidden"
            value="<?php echo $row_activity['Activityname']; ?>"
            name="new_activity_name[]"
            >
          </div>

          <div class="form-group">
            <label>วันที่เริ่มกิจกรรม:&nbsp;</label>
            <?php echo $row_activity[4] ?>&nbsp;<b>ถึง</b>&nbsp;<?php echo $row_activity[5] ?>
            <input type="hidden"
            value="<?php echo $row_activity[4] ?>"
            name="new_activity_startdate[]"
            >
            <input type="hidden"
            name="new_activity_enddate[]"
            value="<?php echo $row_activity[5] ?>"
            >
          </div>

          <div class="form-group">
            <label>ผู้รับผิดชอบกิจกรรม:&nbsp;</label>
            <?php $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_activity['Username']}')";
            $userprofile_rs = mysqli_query($con, $sql);
            while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
                echo "<a href=\"../userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a>";
            } ?>
            <input type="hidden"
            value="<?php echo $row_activity['Username']; ?>"
            name="new_activity_staffname[]"
            >
          </div>

<!-- ######################################################################################################### -->

      <div class="form-group">
        <label>สถานะ:</label>
        <!-- check permission to change status -->
        <?php
        $sql = "SELECT Username FROM tb_project WHERE (Project_id = $project_id)";
        $rs = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($rs)) {
          $username_permission = $row['Username'];
        }
        if (($username_permission == $_SESSION['user_login_name']) ||
          (($row_activity['Username'] == $_SESSION['user_login_name']) && ($row_activity['Status'] != "Finish"))) {
        ?>

        <select class="form-control" id="new_activity_status_<?php echo $row_activity[1] ?>" name="new_activity_status[]">
          <option value="Wait" <?php if ($row_activity['Status'] == "Wait") echo "selected"; ?>>รอดำเนินการ</option>
          <option value="Process" <?php if ($row_activity['Status'] == "Process") echo "selected"; ?>>กำลังดำเนินการ</option>
          <option value="Finish" <?php if ($row_activity['Status'] == "Finish") echo "selected"; ?>>ดำเนินการแล้ว</option>
        </select>

        <?php } else {

          if ($row_activity['Status'] == "Wait") { ?>
            <input type="text"
            class="form-control"
            value="รอดำเนินการ"
            readonly>
            <input type="hidden"
            name="new_activity_status[]"
            value="Wait">

          <?php } elseif ($row_activity['Status'] == "Process") { ?>

            <input type="text"
            class="form-control"
            value="กำลังดำเนินการ"
            readonly>
            <input type="hidden"
            name="new_activity_status[]"
            value="Process">

          <?php } else { ?>

            <input type="text"
            class="form-control"
            value="ดำเนินการแล้ว"
            readonly>
            <input type="hidden"
            name="new_activity_status[]"
            value="Finish">

          <?php }
        } ?>
        </div>
      </div>

    </div> <!-- close container -->
    <hr>
      <?php
        } //close $row_activity
      ?>

      <center>
        <p>
          <button type="submit" class="btn btn-default">บันทึก</button>&nbsp;&nbsp;
          <a href="../project_overview.php?project_id=<?= $project_id ?>&page=<?= $page ?>" class="btn btn-default" role="button">กลับ</a>
        </p>
      </center>

    </form>

  </div> <!-- end container -->

</body>
</html>

<?php
// close connection from database
mysqli_close($con);
?>
