<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

if (isset($_GET['project_id']) && is_numeric($_GET['project_id'])) $project_id = $_GET['project_id'];
else header('location: ../permission/login.php');

$page = $_GET["page"];

// check permission to delete project
$sql = "SELECT Username FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
  if ($row['Username'] != $_SESSION['user_login_name']) header("location: ../permission/login.php");
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
        <li><a href="#"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <li><a href="../search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
        <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

<?php
$sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs)) {
?>

  <div class="container">
    <center>
    <h3>ต้องการลบ <?= $row['Projectname'] ?> ใช่หรือไม่</h3>
    <p>หากลบโครงการไปแล้ว จะไม่สามารถกู้กลับคืนมาได้อีก</p>
    <p>
    <a href="check_delete_project.php?project_id=<?= $project_id ?>&page=<?= $page ?>" class="btn btn-danger" role="button">ใช่ ต้องการลบโครงการ<a>
    <a href="../project_overview.php?project_id=<?= $project_id ?>&page=<?= $page ?>" class="btn btn-default" role="button">กลับ</a>
    </p>
  </center>
  </div> <!-- close container -->
<?php } ?>

</body>
</html>

<?php
// close connection from database
mysqli_close($con);
?>
