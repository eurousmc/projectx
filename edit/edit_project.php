<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Staff")) header("location: ../permission/login.php");

// connect to data base
require("../connect.php");

// check if project ID don't set
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

  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <!-- jQuery core JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- Bootstrap Date-Picker Plugin -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"/>

  <!-- java script -->
  <script>
  var activity_num = <?php
  $sql = "SELECT COUNT(*) FROM tb_activity WHERE (Project_id = $project_id)";
  $rs = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($rs)) {
    echo $row[0];
  }
  ?>; //count activity in project

  var date_pattern = /^([0-9]{4})\-([0-9]{2})\-([0-9]{2})$/;

  // call to delete fileupload
  function call_delete(file_id, filename, project_id) {
    $(document).ready(function(){
      if (confirm("ท่านต้องการลบเอกสาร " + filename + " ใช่หรือไม่?")) {
        $.ajax({
          url: "../delete/check_delete_file.php",
          type: "post",
          data: {"file_id": file_id, "project_id": project_id},
          success: function (response) {
            $("#file_table").html(response);
            alert("เอกสาร " + filename + " ถูกลบแล้ว");
          }
        });
      }
    });
  }

  // for check data before add
  function validateform() {
    var new_project_name = document.getElementById("new_project_name").value;
    var new_project_employer = document.getElementById("new_project_employer").value;
    var new_project_startdate = document.getElementById("new_project_startdate").value;

    if (new_project_name == null || new_project_name == "") {
      alert("โปรดกรอก ชื่อโครงการ");
      return false;
    } else if (new_project_name.length > 50) {
      alert("ชื่อโครงการ ต้องมีความยาวน้อยกว่า 50 ตัวอักษร");
      return false;
    } else if (new_project_employer == null || new_project_employer == "") {
      alert("โปรดกรอก ชื่อหน่วยงาน");
      return false;
    } else if (new_project_employer.length > 50) {
      alert("ชื่อหน่วยงาน ต้องมีความยาวน้อยกว่า 50 ตัวอักษร");
      return false;
    } else if (new_project_startdate == null || new_project_startdate == "") {
      alert("โปรดกรอก วันที่เริ่มโครงการ");
      return false;
    }
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
        <li class="active"><a href="../userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="../userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
        <li><a href="../conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <li><a href="../search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
        <li><a href="../permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <div class="container">

    <form enctype="multipart/form-data" method="post" action="check_edit_project.php?page=<?= $page ?>" onsubmit="return validateform();">
      <input type="hidden" value="<?= $project_id ?>" name="project_id">

<!-- show and edit project data -->
<!-- ######################################################################################################### -->
      <h3>แก้ไขข้อมูลโครงการ</h3>

      <div class="container">

        <?php
          // loop for get data for tb_project and print to javascript
          $sql_project = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
          $rs_project = mysqli_query($con, $sql_project);
          while ($row_project = mysqli_fetch_array($rs_project)) {
        ?>

        <div class="form-group">
          <label>ผู้รับผิดชอบโครงการ: </label>
          <input type="text" class="form-control" id="new_project_staff" name="new_project_staff" placeholder="ชื่อหน่วยงาน ต้องมีความยาวไม่เกิน 50 ตัวอักษร" value="<?php echo $row_project['Username']; ?>" readonly>
        </div>

      <div class="form-group">
        <label for="new_project_name">ชื่อโครงการ:</label>
        <input type="text" class="form-control" id="new_project_name" name="new_project_name" placeholder="ชื่อโครงการ ต้องมีความยาวไม่เกิน 50 ตัวอักษร" value="<?php echo $row_project['Projectname']; ?>" <?php if ($row_project['Username'] == $_SESSION['user_login_name']) {;} else {echo "readonly";} ?>>
      </div>

      <div class="form-group">
        <label for="new_project_employer">ชื่อหน่วยงาน:</label>
        <input type="text" class="form-control" id="new_project_employer" name="new_project_employer" placeholder="ชื่อหน่วยงาน ต้องมีความยาวไม่เกิน 50 ตัวอักษร" value="<?php echo $row_project['Employer']; ?>" <?php if ($row_project['Username'] == $_SESSION['user_login_name']) {;} else {echo "readonly";} ?>>
      </div>

      <div class="form-group">
        <label>วันที่เริ่มโครงการ:</label>
        <div id="sandbox-container">
          <div class="input-daterange input-group" id="datepicker">
            <input type="text" id="new_project_startdate" class="form-control" name="new_project_startdate" value="<?php echo $row_project['Startdate']; ?>">
            <span class="input-group-addon">ถึง</span>
            <input type="text" id="new_project_enddate" class="form-control" name="new_project_enddate" value="<?php echo $row_project['Enddate']; ?>">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="new_project_requirement">ความต้องการ:</label>
        <textarea class="form-control" id="new_project_requirement" name="new_project_requirement" rows="10" <?php if ($row_project['Username'] == $_SESSION['user_login_name']) {;} else {echo "readonly";} ?>><?php echo $row_project['Requirement']; ?></textarea>
      </div>

    </div> <!-- close container -->

      <?php } // close while $row_project ?>
      <hr>
      <center>
      <button type="submit" class="btn btn-default">บันทึก</button>&nbsp;&nbsp;
      <a href="../project_overview.php?project_id=<?= $project_id ?>&page=<?= $page ?>" class="btn btn-default" role="button">กลับ</a>
      </center>
    </form>

  </div> <!-- end container -->

</body>
</html>

<?php
// close connection from database
mysqli_close($con);
?>
