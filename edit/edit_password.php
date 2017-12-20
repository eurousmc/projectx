<?php
// Check user login
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: ../permission/login.php");
// connect to data base
require("../connect.php");

// clear session in add user page
unset($_SESSION['$new_username']);
unset($_SESSION['$new_password']);
unset($_SESSION['$new_confirm_password']);
unset($_SESSION['$new_realname']);
unset($_SESSION['$new_position']);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">

  <link rel="icon" href="../systemSpicture/page_icon.ico">

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
        <li><a href="edit_userpicture.php"><span class="glyphicon">&#xe008;</span>&nbsp;เปลี่ยนรูปภาพประจำตัว</a></li>
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
    <form method="post" action="check_edit_password.php" onsubmit="return validateform();">
    <h3>เปลี่ยนรหัสผ่าน</h3>

    <div class="container">
      <div class="form-group">
        <label for="old_login_password">รหัสผ่านปัจจุบัน:</label>
        <input type="password" class="form-control" id="old_login_password" placeholder="โปรดกรอกรหัสผ่านปัจจุบัน" name="old_login_password">
      </div>
      <div class="form-group">
        <label for="new_login_password">รหัสผ่านใหม่:</label>
        <input type="password" class="form-control" id="new_login_password" placeholder="รหัสผ่านใหม่ ต้องมีความยาว 4-20 ตัวอักษร และต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น" name="new_login_password">
      </div>
      <div class="form-group">
        <label for="new_confirm_login_password">ยืนยันรหัสผ่านใหม่:</label>
        <input type="password" class="form-control" id="new_confirm_login_password" placeholder="โปรดกรอกรหัสผ่านใหม่อีกครั้ง" name="new_confirm_login_password">
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

  <!-- script to check input format -->
  <script>
  function validateform() {
    var name_pattern = /^[A-Za-z0-9]+$/;

    var oldPassword = document.getElementById('old_login_password').value;
    var newPassword = document.getElementById('new_login_password').value;
    var newConfirmPassword = document.getElementById('new_confirm_login_password').value;

    if (oldPassword.length < 4) {
      alert("รหัสผ่านปัจจุบัน ต้องมีความยาวมากกว่า 4 ตัวอักษร");
      return false;
    } else if (oldPassword.length > 20) {
      alert("รหัสผ่านปัจจุบัน ต้องมีความยาวน้อยกว่า 20 ตัวอักษร");
      return false;
    } else if (!name_pattern.test(oldPassword)) {
      alert("รหัสผ่านปัจจุบัน ต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น");
      return false;
    } else if (newPassword.length < 4) {
      alert("รหัสผ่านใหม่ ต้องมีความยาวมากกว่า 4 ตัวอักษร");
      return false;
    } else if (newPassword.length > 20) {
      alert("รหัสผ่านใหม่ ต้องมีความยาวน้อยกว่า 20 ตัวอักษร");
      return false;
    } else if (!name_pattern.test(newPassword)) {
      alert("รหัสผ่านใหม่ ต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น");
      return false;
    } else if (newConfirmPassword != newPassword) {
      alert("ยืนยันรหัสผ่านใหม่ ต้องตรงกับ รหัสผ่านใหม่");
      return false;
    }
  }

  </script>

</body>
</html>
<?php
// close connection from database
mysqli_close($con);
?>
