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
  <link rel="icon" href="picture/page_icon.ico">
  <title>ระบบวิเคราะห์และติดตามงานพัฒนาซอฟต์แวร์</title>

  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <!-- jQuery core JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <script>
  $(document).ready(function(){

    function Load_messages() {

      $.ajax({
        url: "messages.php",
        success: function (response) {
          $("#message").html(response);
        }
      });

      $.ajax({
        url: "showuser.php",
        success: function (response) {
          $("#showuser").html(response);
        }
      });

    }

    setInterval(function() {
      Load_messages()
    }, 1000);

    function Load_sidebar() {
      $.ajax({
        url: "sidebar.php",
        success: function (response) {
          $("#sidebar").html(response);
        }
      });
    }

    setInterval(function() {
      Load_sidebar()
    }, 10000);

    $.ajax({
      url: "sidebar.php",
      success: function (response) {
        $("#sidebar").html(response);
      }
    });

    $.ajax({
      url: "showuser.php",
      success: function (response) {
        $("#showuser").html(response);
      }
    });

    $.ajax({
      url: "newmessage.php",
      success: function (response) {
        $("#newmessage").html(response);
      }
    });

  });
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
          <li><a href="../search/clear_search_project.php"><span class="glyphicon">&#xe021;</span>&nbsp;หน้าแรก</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="../userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="../userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
          <li><a href="chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
          <?php if ($_SESSION['user_login_position'] != "Administrator") { ?>
            <li><a href="../search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
          <?php } ?>
          <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
        </ul>
      </div>

    </div>
  </nav>

<div class="container">
  <div class="row">

    <div class="col-sm-4" style="width: auto; height: 450px; overflow-y: scroll; scrollbar-arrow-color:blue; scrollbar-face-color: #e7e7e7; scrollbar-3dlight-color: #a0a0a0; scrollbar-darkshadow-color:#888888">
      <div id="sidebar"></div>
    </div>

    <div class="col-sm-8">
      <div class="row">
        <div id="showuser"></div>
      </div>
      <div class="row" style="width: 100%; height: 300px; overflow-y: scroll; scrollbar-arrow-color:blue; scrollbar-face-color: #e7e7e7; scrollbar-3dlight-color: #a0a0a0; scrollbar-darkshadow-color:#888888">
        <div id="message" ></div>
      </div>
      <div class="row">
        <div id="newmessage"></div>
      </div>
    </div>

  </div>

</div>

</body>
</html>

<?php mysqli_close($con); ?>
