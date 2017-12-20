<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

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

  <!-- Bootstrap Date-Picker Plugin -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"/>

  <!-- Script for validateform -->
  <script>
  var activity_num = 0; // count activity in project

  var date_pattern = /^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/;

  // for check data before add
  function validateform() {
    var new_project_name = document.getElementById("new_project_name").value;
    var new_project_employer = document.getElementById("new_project_employer").value;
    var new_project_startdate = document.getElementById("new_startdate_0").value;
    var new_project_enddate = document.getElementById("new_enddate_0").value;
    var new_project_daybetween = document.getElementById("new_daybetween_0").value;

    if (new_project_name == null || new_project_name == "") {
      alert("โปรดกรอก ชื่อโครงการ");
      document.getElementById("new_project_name").focus();
      return false;
    } else if (new_project_name.length > 50) {
      alert("ชื่อโครงการ ต้องมีความยาวน้อยกว่า 50 ตัวอักษร");
      document.getElementById("new_project_name").focus();
      return false;
    } else if (new_project_employer == null || new_project_employer == "") {
      alert("โปรดกรอก ชื่อหน่วยงาน");
      document.getElementById("new_project_employer").focus();
      return false;
    } else if (new_project_employer.length > 50) {
      alert("ชื่อหน่วยงาน ต้องมีความยาวน้อยกว่า 50 ตัวอักษร");
      document.getElementById("new_project_employer").focus();
      return false;
    } else if (new_project_startdate == null || new_project_startdate == "") {
      alert("โปรดกำหนด วันที่เริ่มโครงการ");
      document.getElementById("new_startdate_0").focus();
      return false;
    } else if (new_project_enddate == null || new_project_enddate == "") {
      alert("โปรดกำหนด วันที่สิ้นสุดโครงการ");
      document.getElementById("new_enddate_0").focus();
      return false;
    } else if (new_project_daybetween == 0) {
      alert("โปรดกำหนด ระยะเวลาของโครงการ");
      document.getElementById("new_enddate_0").focus();
      return false;
    } else if (activity_num <= 0) {
      alert("คุณต้องเพิ่มกิจกรรมอย่างน้อย 1 กิจกรรม");
      document.getElementById("add_activity").click();
      document.getElementById("new_activity_name_1").focus();
      return false;
    }

    // check activity name and date is not null
    for (var i = 1; i <= activity_num; i++) {

      var name_value = document.getElementById("new_activity_name_" + i).value;
      var startdate_value = document.getElementById("new_startdate_" + i).value;
      var enddate_value = document.getElementById("new_enddate_" + i).value;
      var daybetween_value = document.getElementById("new_daybetween_" + i).value;

      if (name_value == null || name_value == "") {
        alert("โปรดกรอก ชื่อกิจกรรมที่ " + i);
        document.getElementById("new_activity_name_" + i).focus();
        return false;
      } else if (name_value.length > 50) {
        alert("ชื่อกิจกรรมที่ " + i + " ต้องมีความยาวน้อยกว่า 50 ตัวอักษร");
        document.getElementById("new_activity_name_" + i).focus();
        return false;
      } else if (startdate_value == null || startdate_value == "") {
        alert("โปรดกำหนด วันที่เริ่มกิจกรรมที่ " + i);
        document.getElementById("new_startdate_" + i).focus();
        return false;
      } else if (enddate_value == null || enddate_value == "") {
        alert("โปรดกำหนด วันที่สิ้นสุดกิจกรรมที่ " + i);
        document.getElementById("new_enddate_" + i).focus();
        return false;
      } else if (daybetween_value == 0) {
        alert("โปรดกำหนด ระยะเวลาของกิจกรรมที่ " + i);
        document.getElementById("new_enddate_" + i).focus();
        return false;
      }
    }

  } // close validateform

  // for show day between
  function cal_show_date(id) {
    var start = document.getElementById("new_startdate_" + id).value;
    var end = document.getElementById("new_enddate_" + id).value;
    document.getElementById("new_daybetween_" + id).value = project_day = find_day(start, end);
  }

  // for find day between
  function find_day(start, end) {

    var firstDate = new Date(start);
    var secondDate = new Date(end);
    var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

    return diffDays;
  }

  // jQuery code here --------------------------------------------------------------------------------------
  $(document).ready(function(){

    // for datepicker input project startdate and enddate
    $('#sandbox-container .input-daterange').datepicker({
      format: "yyyy-mm-dd",
      language: "th",
      daysOfWeekHighlighted: "0,6",
      autoclose: true,
      todayHighlight: true
    });

    // for create activity form input
    $("#add_activity").click(function(){

      // for check if user don't set project day
      if ($("#new_daybetween_0").val() == 0) {
        alert("โปรดกำหนด ระยะเวลาของโครงการก่อนสร้างกิจกรรม");
        $("#new_enddate_0").focus();
      } else {

      activity_num++;

      $("#activity_input").append("<div class=\"container\" id=\"activity_" + activity_num + "\"></div>");

      $("#activity_" + activity_num).append("<h4>กิจกรรมที่ " + activity_num + "</h4>\
      <div class=\"form-group\">\
        <label>ชื่อกิจกรรม:</label>\
        <input type=\"text\" class=\"form-control\" id=\"new_activity_name_" + activity_num + "\" name=\"new_activity_name[]\" placeholder=\"ชื่อกิจกรรม ต้องมีความยาวไม่เกิน 50 ตัวอักษร\">\
      </div>");

      $("#activity_" + activity_num).append("<div class=\"form-group\">\
        <label>วันที่เริ่ม และ วันที่สิ้นสุด:</label>\
        <div id=\"sandbox-container\">\
          <div class=\"input-daterange input-group\" id=\"datepicker\">\
            <input type=\"text\" id=\"new_startdate_" + activity_num + "\" class=\"form-control\" name=\"new_activity_startdate[]\" value=\"<?php echo date('Y-m-d'); ?>\" onchange=\"cal_show_date(" + activity_num + ")\">\
            <span class=\"input-group-addon\">ถึง</span>\
            <input type=\"text\" id=\"new_enddate_" + activity_num + "\" class=\"form-control\" name=\"new_activity_enddate[]\" value=\"<?php echo date('Y-m-d'); ?>\" onchange=\"cal_show_date(" + activity_num + ")\">\
          </div>\
        </div>\
      </div>");

      $("#activity_" + activity_num).append("<div class=\"form-group\">\
        <label>ระยะเวลาที่ใช้(วัน):</label>\
        <input type=\"text\" id=\"new_daybetween_" + activity_num + "\" class=\"form-control\" value=\"0\" readonly>\
      </div>");

      $("#activity_" + activity_num).append("<div class=\"form-group\">\
        <label>ผู้รับผิดชอบกิจกรรม:</label>\
        <select class=\"form-control\" id=\"new_activity_staffname_" + activity_num + "\" name=\"new_activity_staffname[]\"></selected>");

          <?php
          $sql = "SELECT * FROM tb_user WHERE (Position = 'Staff')";
          $rs = mysqli_query($con, $sql);
          while ($row = mysqli_fetch_array($rs)) {
          ?>
      $("#new_activity_staffname_" + activity_num).append("<option value=\"<?php echo $row[0]; ?>\" <?php if ($row[0] == $_SESSION['user_login_name']) echo "selected"; ?>><?php echo $row[2]; ?></option>");
        <?php } ?>

      $("#activity_" + activity_num).append("<div class=\"form-group\">\
        <label for=\"new_project_file\">เพิ่มเอกสารเกี่ยวกับโครงการ:</label>\
        <input type=\"file\" id=\"new_activity_file_" + activity_num + "\" name=\"new_activity_file_" + activity_num + "[]\" multiple>\
        <small class=\"form-text text-muted\">เอกสารจะต้องมีขนาดไม่เกิน 8 MB. ต่อหนึ่งเอกสาร และสามารถเพิ่มได้มากกว่าหนึ่งเอกสาร</small>\
      </div>");


      // for set datepicker
      $('#sandbox-container .input-daterange').datepicker({
        format: "yyyy-mm-dd",
        startDate: $('#new_startdate_0').val(),
        endDate: $('#new_enddate_0').val(),
        language: "th",
        daysOfWeekHighlighted: "0,6",
        autoclose: true,
        todayHighlight: true
      });

    }// close if ($("#new_daybetween_0").val() == 0) {

    }); // close create activity form input

    // for remove activity form input
    $("#remove_activity").click(function(){
      if (activity_num != 1) {
        $("#activity_" + activity_num).remove();
        activity_num--;
      }
    });

  }); // close jquery

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
        <li><a href="../search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
        <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <div class="container">

    <form enctype="multipart/form-data" method="post" action="check_add_project.php" onsubmit="return validateform();">
      <h3>กำหนดข้อมูลโครงการ</h3>

      <div class="container">

        <div class="form-group">
          <label>ผู้รับผิดชอบโครงการ: </label>
          <input type="text" class="form-control" value="<?php echo $_SESSION['user_login_realname']; ?>" readonly>
        </div>

      <div class="form-group">
        <label for="new_project_name">ชื่อโครงการ:</label>
        <input type="text" class="form-control" id="new_project_name" name="new_project_name" placeholder="ชื่อโครงการ ต้องมีความยาวไม่เกิน 50 ตัวอักษร">
      </div>

      <div class="form-group">
        <label for="new_project_employer">ชื่อหน่วยงาน:</label>
        <input type="text" class="form-control" id="new_project_employer" name="new_project_employer" placeholder="ชื่อหน่วยงาน ต้องมีความยาวไม่เกิน 50 ตัวอักษร">
      </div>

      <div class="form-group">
        <label>วันที่เริ่ม และ วันที่สิ้นสุด:</label>
        <div id="sandbox-container">
          <div class="input-daterange input-group" id="datepicker">
            <input type="text" id="new_startdate_0" class="form-control" name="new_project_startdate" value="<?php echo date('Y-m-d'); ?>" onchange="cal_show_date(0)">
            <span class="input-group-addon">ถึง</span>
            <input type="text" id="new_enddate_0" class="form-control" name="new_project_enddate" value="<?php echo date('Y-m-d'); ?>" onchange="cal_show_date(0)">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>ระยะเวลาที่ใช้พัฒนา(วัน):</label>
        <input type="text" id="new_daybetween_0" class="form-control" value="0" readonly>
      </div>

      <div class="form-group">
        <label for="new_project_requirement">ความต้องการ:</label>
        <textarea class="form-control" id="new_project_requirement" name="new_project_requirement" rows="10"></textarea>
      </div>

      <div class="form-group">
        <label for="new_project_file">เพิ่มเอกสารเกี่ยวกับโครงการ:</label>
          <input type="file" id="new_project_file" name="new_project_file[]" multiple>
          <small class="form-text text-muted">เอกสารจะต้องมีขนาดไม่เกิน 8 MB. ต่อหนึ่งเอกสาร และสามารถเพิ่มได้มากกว่าหนึ่งเอกสาร</small>
      </div>

      </div><!-- end container -->

      <hr>
      <h3>กำหนดข้อมูลกิจกรรม</h3>

      <span id="activity_input"></span>

      <div class="container">
        <p>&nbsp;</p>
        <button type="button" class="btn btn-default" id="add_activity"><span class="glyphicon">&#x2b;</span>&nbsp;เพิ่มกิจกรรม</button>&nbsp;&nbsp;
        <button type="button" class="btn btn-default" id="remove_activity"><span class="glyphicon">&#x2212;</span>&nbsp;ลบกิจกรรม</button>
      </div>

      <hr>
      <center>
        <p>
          <button type="submit" class="btn btn-default">สร้างโครงการ</button>&nbsp;&nbsp;
          <a href="../index.php" class="btn btn-default" role="button">กลับ</a>
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
