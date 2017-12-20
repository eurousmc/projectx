<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Administrator")) header("location: ../permission/login.php");

$page = $_GET['page'];
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

  <!-- script to check input format -->
  <script>
  function validateform() {
    var name_pattern = /^[A-Za-z0-9]+$/;

    var newUsername = document.inputform.new_login_username.value;
    var newPassword = document.inputform.new_login_password.value;
    var newConfirmPassword = document.inputform.new_confirm_login_password.value;
    var newRealName = document.inputform.new_login_realname.value;
    var newPosition = document.inputform.new_login_position.value;

    if (newUsername.length < 4) {
      alert("ชื่อบัญชีผู้ใช้งาน ต้องมีความยาวมากกว่า 4 ตัวอักษร");
      return false;
    } else if (newUsername.length > 20) {
      alert("ชื่อบัญชีผู้ใช้งาน ต้องมีความยาวน้อยกว่า 20 ตัวอักษร");
      return false;
    } else if (!name_pattern.test(newUsername)) {
      alert("ชื่อบัญชีผู้ใช้งาน ต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น");
      return false;
    } else if (newPassword.length < 4) {
      alert("รหัสผ่าน ต้องมีความยาวมากกว่า 4 ตัวอักษร");
      return false;
    } else if (newPassword.length > 20) {
      alert("รหัสผ่าน ต้องมีความยาวน้อยกว่า 20 ตัวอักษร");
      return false;
    } else if (!name_pattern.test(newPassword)) {
      alert("รหัสผ่าน ต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น");
      return false;
    } else if (newConfirmPassword != newPassword) {
      alert("ยืนยันรหัสผ่านต้องตรงกับรหัสผ่าน");
      return false;
    } else if (newRealName.length < 4) {
      alert("ชื่อผู้ใช้งาน ต้องมีความยาวมากกว่า 4 ตัวอักษร");
      return false;
    } else if (newRealName.length > 50) {
      alert("ชื่อผู้ใช้งาน ต้องมีความยาวน้อยกว่า 50 ตัวอักษร");
      return false;
    } else if (newPosition == null || newPosition == "") {
      alert("โปรดเลือกตำแหน่งงานก่อนกดบันทึก");
      return false;
    }
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
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="../userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="../userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
        <li><a href="../conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <div class="container">
    <h3>เพิ่มบัญชีผู้ใช้งาน</h3>

    <form method="post" enctype="multipart/form-data" action="check_add_user.php" onsubmit="return validateform();" name="inputform" >

      <div class="container">

        <div class="form-group">
          <label for="new_login_username">ชื่อบัญชีผู้ใช้งาน:</label>
          <input type="text" class="form-control" id="new_login_username" placeholder="ชื่อบัญชีผู้ใช้ ต้องมีความยาว 4-20 ตัวอักษร และต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น" name="new_login_username" value="<?php if (isset($_SESSION['new_username'])) echo $_SESSION['new_username']; ?>">
        </div>

        <div class="form-group">
          <label for="new_login_password">รหัสผ่าน:</label>
          <input type="password" class="form-control" id="new_login_password" placeholder="รหัสผ่าน ต้องมีความยาว 4-20 ตัวอักษร และต้องเป็นภาษาอังกฤษหรือตัวเลขอาราบิกเท่านั้น" name="new_login_password" value="<?php if (isset($_SESSION['new_password'])) echo $_SESSION['new_password']; ?>">
        </div>

        <div class="form-group">
          <label for="new_confirm_login_password">ยืนยันรหัสผ่าน:</label>
          <input type="password" class="form-control" id="new_confirm_login_password" placeholder="โปรดกรอกรหัสผ่านอีกครั้ง" name="new_confirm_login_password" value="<?php if (isset($_SESSION['new_confirm_password'])) echo $_SESSION['new_confirm_password']; ?>">
        </div>

        <div class="form-group">
          <label for="new_login_realname">ชื่อผู้ใช้งาน:</label>
          <input type="text" class="form-control" id="new_login_realname" placeholder="ชื่อผู้ใช้งานต้องมีความยาวตั้งแต่ 4-50 ตัวอักษร" name="new_login_realname" value="<?php if (isset($_SESSION['new_realname'])) echo $_SESSION['new_realname']; ?>">
        </div>

        <div class="form-group">
          <label  for="new_login_position">ตำแหน่งงาน:</label>
          <div class="radio">
            <label><input type="radio" id="new_login_position" name="new_login_position" value="Administrator" <?php if (isset($_SESSION['new_position']) && $_SESSION['new_position'] == "Administrator") echo "checked"; ?>>ผู้ดูแลระบบ</label>
          </div>
          <div class="radio">
            <label><input type="radio" id="new_login_position" name="new_login_position" value="Manager" <?php if (isset($_SESSION['new_position']) && $_SESSION['new_position'] == "Manager") echo "checked"; ?>>ผู้บริหาร</label>
          </div>
          <div class="radio">
            <label><input type="radio" id="new_login_position" name="new_login_position" value="Staff" <?php if (isset($_SESSION['new_position']) && $_SESSION['new_position'] == "Staff") echo "checked"; ?>>ผู้ปฏิบัติงาน</label>
          </div>
        </div>

        <div class="form-group">
          <label for="new_login_picture">รูปภาพ:</label>
          <input type="file" id="new_login_picture" name="new_login_picture">
          <small class="form-text text-muted">รูปภาพจะต้องเป็นไฟล์ *.jpg, *.jpeg, *.png และขนาดไม่เกิน 8 MB.</small>
        </div>

        <!-- to send page -->
        <input type="hidden" value="<?= $page ?>" name="page">

      </div>

      <center>
        <p>
          <button type="submit" class="btn btn-default"><span class="glyphicon">&#xe172;</span>&nbsp;บันทึก</button>&nbsp;&nbsp;
          <a href="../manage_user.php?page=<?= $page ?>" class="btn btn-default" role="button">กลับ</a>
        </p>
      </center>

      </form>
  </div> <!-- close main container -->

  <!-- show error msg -->
  <?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>
</body>
</html>
