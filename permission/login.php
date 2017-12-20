<?php
// if login already just bypass to index.php
session_start();
if (isset($_SESSION['user_login_position'])) header("location: ../index.php");
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
  $( document ).ready(function() {
    $("#login_username").focus();
  });
  </script>

</head>
<body>
<div class="container"><h1>ระบบวิเคราะห์และติดตามงานพัฒนาซอฟต์แวร์</h1></div>

  <div class="container">
    <div class="container">
      <form method="post" action="check_login.php">

        <div class="form-group">
          <label for="login_username">ชื่อผู้ใช้:</label>
          <input type="text" class="form-control" id="login_username" placeholder="Username" name="login_username">
        </div>

        <div class="form-group">
          <label for="login_password">รหัสผ่าน:</label>
          <input type="password" class="form-control" id="login_password" placeholder="Password" name="login_password">
        </div>

        <button type="submit" class="btn btn-default" id="login_submit"><span class="glyphicon">&#xe161;</span>&nbsp;เข้าสู่ระบบ</button>

      </form>
      <p>
        <?php
        if (isset($_GET['msg']))
        echo "<div class=\"alert alert-danger\">{$_GET['msg']}</div>";
        ?>
      </p>
    </div>
  </div>

</body>
</html>
