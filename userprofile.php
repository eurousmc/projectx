<?php
// Check user login
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: permission/login.php");

// connect to data base
require("connect.php");

if (isset($_GET['username']))
  $username = $_GET['username'];
else
  header("location: permission/login.php");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">

  <link rel="icon" href="systempicture/page_icon.ico">

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
        url: "badges/message_badges.php",
        success: function (response) {
          $("#message_badges").html(response);
        }
      });
      $.ajax({
        url: "badges/notification_badges.php",
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
        <li><a href="index.php"><span class="glyphicon">&#xe021;</span>&nbsp;หน้าแรก</a></li>
        <?php
        if ($username == $_SESSION['user_login_name'])
        echo "<li><a href=\"edit/edit_userpicture.php\"><span class=\"glyphicon\">&#xe008;</span>&nbsp;เปลี่ยนรูปภาพประจำตัว</a></li>
        <li><a href=\"edit/edit_password.php\"><span class=\"glyphicon\">&#xe065;</span>&nbsp;เปลี่ยนรหัสผ่าน</a></li>";
        ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
        <li><a href="conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <?php if ($_SESSION['user_login_position'] != "Administrator") { ?>
        <li><a href="search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
        <?php } ?>
        <li><a href="permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <?php
  $sql = "SELECT * FROM tb_user WHERE (Username = '$username')";
  $rs = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($rs)) {
  ?>

  <div class="container">

    <h3>ข้อมูลเกี่ยวกับ&nbsp;<?php echo $row[2]; ?>
    </h3>

    <div class="container">
    <p>
      <img src="userpicture/<?php echo $row[4]; ?>" style="height: 200px; width: auto;">
    </p>

    <p>
      <label>ตำแหน่งงาน:</label>&nbsp;
      <?php
      if ($row[3] == "Administrator") {
        echo "ผู้ดูแลระบบ";
      } else if ($row[3] == "Manager") {
        echo "ผู้บริหาร";
      } else {
        echo "ผู้ปฏิบัติงาน";
      }
      ?>
    </p>
<!-- ################################################################################# -->
<?php if ($row['Position'] == "Staff") { ?>
  <p><b>โครงการที่รับผิดชอบ:&nbsp;</b><?php
  $sql = "SELECT COUNT(Project_id) FROM tb_project WHERE (Username = '$username')";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) $project_count_num = $count_row[0];
  echo $project_count_num;
  ?></p>

  <p><b>โครงการที่มีส่วนร่วม:</b>&nbsp;<?php
  $sql = "SELECT COUNT(*)
  FROM tb_project
  WHERE (Project_id IN (SELECT Project_id FROM tb_activity WHERE (Username = '$username')))
  ORDER BY Project_id DESC";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) echo $count_row[0];
  ?></p>

  <p><b>โครงการที่สำเร็จ:&nbsp;</b><?php
  $sql = "SELECT COUNT(Username) FROM tb_project WHERE ((Username = '$username') AND (Result = 'Successfully'))";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) $project_success = $count_row[0];
  echo $project_success;
  if ($project_success != 0)
  echo "&nbsp;(".round(($project_success * 100) / $project_count_num)."%)";
  ?></p>

  <p><b>โครงการที่ล้มเหลว:&nbsp;</b><?php
  $sql = "SELECT COUNT(Username) FROM tb_project WHERE ((Username = '$username') AND (Result = 'Failed'))";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) $project_fail = $count_row[0];
  echo $project_fail;
  if ($project_fail != 0)
  echo "&nbsp;(".round(($project_fail * 100) / $project_count_num)."%)";
  ?></p>

  <p><b>กิจกรรมที่รับผิดชอบ:&nbsp;</b><?php
  $sql = "SELECT COUNT(Username) FROM tb_activity WHERE (Username = '$username')";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) $activity_count_num = $count_row[0];
  echo $activity_count_num;
  ?></p>

  <p><b>กิจกรรมที่สำเร็จ:&nbsp;</b><?php
  $sql = "SELECT COUNT(Username) FROM tb_activity WHERE ((Username = '$username') AND (Result = 'Successfully'))";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) $activity_success = $count_row[0];
  echo $activity_success;
  if ($activity_success != 0)
  echo "&nbsp;(".round(($activity_success * 100) / $activity_count_num)."%)";
  ?></p>

  <p><b>กิจกรรมที่ล้มเหลว:&nbsp;</b><?php
  $sql = "SELECT COUNT(Username) FROM tb_activity WHERE ((Username = '$username') AND (Result = 'Failed'))";
  $count_rs = mysqli_query($con, $sql);
  while ($count_row = mysqli_fetch_array($count_rs)) $activity_fail = $count_row[0];
  echo $activity_fail;
  if ($activity_fail != 0)
  echo "&nbsp;(".round(($activity_fail * 100) / $activity_count_num)."%)";
  ?></p>

<?php } ?>
</div>
<!-- ################################################################################# -->
<center>
  <p>
    <a href="index.php" class="btn btn-default" role="button">กลับ</a>
  </p>
</center>

</div> <!-- close main container -->

<?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>

</body>
</html>

<?php
} // close loop while $row

// close connection from database
mysqli_close($con);
?>
