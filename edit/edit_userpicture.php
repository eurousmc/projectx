<?php
// Check user login
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");
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

<script>
// to change profile picture befor upload
function show_pic(input) {
  $(document).ready(function(){
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#profile_pic')
      .attr('src', e.target.result)
      .height(200);
    };
    reader.readAsDataURL(input.files[0]);
  }); // close jquery
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
        <li><a href="edit_password.php"><span class="glyphicon">&#xe065;</span>&nbsp;เปลี่ยนรหัสผ่าน</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="../userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="../userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
        <li><a href="../conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <?php if ($_SESSION['user_login_position'] != "Administrator") { ?>
        <li><a href="../search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
        <?php } ?>
        <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <div class="container">
    <form method="post" enctype="multipart/form-data" action="check_edit_userpicture.php">
    <h3>เปลี่ยนรูปภาพประจำตัว</h3>

    <div class="container">
      <?php
        $sql = "SELECT * FROM tb_user WHERE (Username = '{$_SESSION['user_login_name']}')";
        $rs = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($rs)) {
      ?>
      <p>
        <img src="../userpicture/<?php echo $row[4]; ?>" style="height: 200px; width: auto;" id="profile_pic">
      </p>
      <?php } ?>
      <div class="form-group">
        <label for="new_login_picture">รูปภาพ:</label>
        <input type="file" id="new_login_picture" name="new_login_picture" onchange="show_pic(this)">
        <small class="form-text text-muted">รูปภาพจะต้องเป็นไฟล์ *.jpg, *.jpeg, *.png และขนาดไม่เกิน 8 MB.</small>
      </div>
    </div>

    <center>
      <p>
        <button type="submit" class="btn btn-default"><span class="glyphicon">&#xe172;</span>&nbsp;บันทึก</button>&nbsp;&nbsp;
        <a href="../userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>" class="btn btn-default" role="button">กลับ</a>
      </p>
    </center>

    </form>
  </div>

  <!-- show error msg -->
  <?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>
</body>
</html>
