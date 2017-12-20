<?php
// start session
session_start();

if (!isset($_SESSION['user_login_position'])) header("location: permission/login.php");

// เชื่อมต่อกับฐานข้อมูล
require("connect.php");

// รับรหัสโครงการ
$project_id = $_GET["project_id"];
// รับเลขหน้าที่เปิดล่าสุด
if (isset($_GET["page"])) $page = $_GET["page"];
else $page = 1;

?>

<!DOCTYPE html>
<html>
<head>

  <meta charset="utf-8">

  <link rel="icon" href="picture/page_icon.ico">

  <title>ระบบวิเคราะห์และติดตามงานพัฒนาซอฟต์แวร์</title>

  <!-- download Google chart -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <!-- jQuery core JavaScript -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <!-- pie charts select ////////////////////////////////////////////////////////////////////////////////-->
  <?php
  $sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id)";
  $result = mysqli_query($con, $sql);
  $pie_chart_data = "";
  while($row = mysqli_fetch_array($result)){

    $date1 = date_create($row["Startdate"]);
    $date2 = date_create($row["Enddate"]);
    $diff = date_diff($date1, $date2);

    $pie_chart_data = $pie_chart_data."['{$row["Activityname"]}', {$diff->days}],";

  }
  $pie_chart_data = substr($pie_chart_data, 0, -1);
  ?>

  <script type="text/javascript">

    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = google.visualization.arrayToDataTable([
      ['Task', 'Hours per Day'],
      <?= $pie_chart_data ?>
      ]);

      var options = {
        is3D: true,
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));
      chart.draw(data, options);
    }

    //--------------------------------------------------------------------------

    function printContent(el){
      var restorepage = document.body.innerHTML;
      var printcontent = document.getElementById(el).innerHTML;
      document.body.innerHTML = printcontent;
      window.print();
      document.body.innerHTML = restorepage;
    }

  </script>

  <!-- //Grantt chart///////////////////////////////////////////////////////////////////////////-->
  <?php
  $sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id)";
  $result = mysqli_query($con, $sql);
  $dmy_Grantt = "";

  while ($row = mysqli_fetch_array($result)) {

    // หาสถานะของกิจกรรม
    $dmy_start = str_replace("-", ",", $row["Startdate"]);
    $dmy_end = str_replace("-", ",", $row["Enddate"]);
    if ($row["Status"] == "Wait") $THstatus = "(รอดำเนินการ)";
    if ($row["Status"] == "Process") $THstatus = "(กำลังดำเนินการ)";
    if ($row["Status"] == "Finish") $THstatus = "(ดำเนินการแล้ว)";

    $sql = "SELECT * FROM tb_user WHERE (Username = '{$row['Username']}')";
    $userprofile_rs = mysqli_query($con, $sql);
    while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
      $grantt_realname = $userprofile_row['Realname'];
    }

    $dmy_Grantt = $dmy_Grantt."[ '{$row["Activityname"]} $THstatus','{$grantt_realname}',new Date($dmy_start),
    new Date($dmy_end)],";
  }
  $dmy_Grantt = substr($dmy_Grantt, 0, -1);
  ?>

  <script type="text/javascript">
    google.charts.load("current", {packages:["timeline"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var container = document.getElementById('Grantt');
      var chart = new google.visualization.Timeline(container);
      var dataTable = new google.visualization.DataTable();

      dataTable.addColumn({ type: 'string', id: 'Term' });
      dataTable.addColumn({ type: 'string', id: 'Name' });
      dataTable.addColumn({ type: 'date', id: 'Start' });
      dataTable.addColumn({ type: 'date', id: 'End' });

      dataTable.addRows([
      <?= $dmy_Grantt ?>
      ]);

      chart.draw(dataTable);
    }
  </script>
  <!--///////////////////////////////////////////////////////////////////////////////// -->

  <!-- columnchart //////////////////////////////////////////////////////////////////////// -->
  <?php
  $sql = "SELECT * FROM tb_activity WHERE (Project_id = $project_id)";
  $result = mysqli_query($con, $sql);
  $columnchart_duo = "";
  while ($row = mysqli_fetch_array($result)) {
    // ตัดเป็นจำนวนวัน
    $start = strtotime($row["Startdate"]);
    $end = strtotime($row["Enddate"]);
    $days_between_set = ceil(abs($end - $start) / 86400);

    if ($row["Realenddate"] == "-") {
      $days_between_unset = 0 ;
    } else {
      $end = strtotime($row["Realenddate"]);
      $days_between_unset = ceil(abs($end - $start) / 86400);
    }

    $columnchart_duo = $columnchart_duo."['{$row["Activityname"]}', $days_between_set, $days_between_unset],";

  }
  $columnchart_duo = substr($columnchart_duo, 0, -1);
  ?>

  <script type="text/javascript">
    google.charts.load('current', {'packages':['bar']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['ระยะเวลาในกิจกรรม', 'ระยะเวลาที่กำหนดไว้(วัน)', 'ระยะเวลาที่ใช้จริง(วัน)'],
        <?= $columnchart_duo ?>
      ]);

      var options = {
        chart: {
        }
      };

      var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>

  <script>
    function printContent(el){
      var restorepage = document.body.innerHTML;
      var printcontent = document.getElementById(el).innerHTML;
      document.body.innerHTML = printcontent;
      window.print();
      document.body.innerHTML = restorepage;
    }
  </script>

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

        <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">จัดการโครงการ
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
        <?php
        // check if project is not finish
        $sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
        $rs = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($rs)) {
          if ($row['Result'] == "-") {

            $sql = "SELECT Username FROM tb_project WHERE (Project_id = $project_id)";
            $rs = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($rs)) {
              if ($row['Username'] == $_SESSION['user_login_name']) {
                ?>
                <li><a href="edit/edit_project.php?project_id=<?= $project_id ?>&page=<?= $page ?>"><span class="glyphicon">&#xe032;</span> แก้ไขข้อมูลโครงการ</a></li>
                <?php
              }
            }
            ?>
            <?php if ($_SESSION['user_login_position'] == "Staff") { ?>
              <li><a href="edit/edit_activity.php?project_id=<?= $project_id ?>&page=<?= $page ?>"><span class="glyphicon">&#xe109;</span> แก้ไขสถานะกิจกรรม</a></li>
              <li><a href="edit/edit_fileupload.php?project_id=<?= $project_id ?>&page=<?= $page ?>"><span class="glyphicon">&#xe198;</span> แก้ไขเอกสารเกี่ยวกับโครงการ</a></li>
              <li><a href="edit/edit_fileupload_activity.php?project_id=<?= $project_id ?>&page=<?= $page ?>"><span class="glyphicon">&#xe198;</span> แก้ไขเอกสารเกี่ยวกับกิจกรรม</a></li>
              <?php
            } // close if ($_SESSION['user_login_position'] == "Staff") {
            } // close if ($row['Result'] == "-") {
            } // close while check if project is not finish
            ?>
            <?php
            $sql = "SELECT Username FROM tb_project WHERE (Project_id = $project_id)";
            $rs = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($rs)) {
              if ($row['Username'] == $_SESSION['user_login_name']) {
                ?>
                <li><a href="delete/delete_project.php?project_id=<?= $project_id ?>&page=<?= $page ?>"><span class="glyphicon">&#xe020;</span> ลบโครงการ</a></li>
                <?php
              }
            }
            ?>
          </ul>
        </li>

            <li><a href="javascript:;" onclick="printContent('div_print')"><span class="glyphicon">&#xe045;</span> พิมพ์รายงาน</a></li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li><a href="userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
            <li><a href="conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
            <li><a href="search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
            <li><a href="permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
          </ul>
        </div>
      </div>
      </nav>

      <?php
      // ผู้ร่วมโครงการ //////////////////////////////////////////////////////////////////////////////////////////
      $sql_include_project = "SELECT Username FROM tb_activity WHERE (Project_id = $project_id)";
      $result_include = mysqli_query($con , $sql_include_project);

      // วนลูป เพื่อหาผู้ร่วมโครงการทั้งหมดแล้วเก็บไว้ใน อาเรย์ $name_include_array
      $num = 0;
      while ($row_include = mysqli_fetch_array($result_include)) {

        // วนลูปเพื่อหาชื่อจริงของผู้ร่วมโครงการ
        $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_include['Username']}')";
        $userprofile_rs = mysqli_query($con, $sql);
        while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
          //$name_include_array[$num] = $userprofile_row['Realname'];
          $name_include_array[$num] = "<a href=\"userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a>";
        }
        $num++;

      }

      //ตัดชื่อที่ซ้ำกัน
      $name_include = array_unique($name_include_array);

      // นำชื่อทั้งหมดมาต่อกัน เพื่อนำไปแสดงผล
      $show_name_include="";
      foreach ($name_include as $value) {
        $show_name_include = $value.", ".$show_name_include;
      }
      $show_name_include = substr($show_name_include, 0,-2);

      // ตัด string /////////////////////////////////////////////////////////////////////////////////////////////

      //รายละเอียดโครงการ ////////////////////////////////////////////////////////////////////////////////////////
      $sql = "SELECT * FROM tb_project WHERE (Project_id = $project_id)";
      $result = mysqli_query($con , $sql);
      while($row = mysqli_fetch_array($result)){
        ?>

        <div class="container">

          <div id="div_print">

                <h3>ข้อมูลโครงการ</h3>
                <div style="margin:30px;">
                  <p><b>ชื่อโครงการ : </b><?php echo $row["Projectname"]; ?></p>
                  <p><b>ผู้รับผิดชอบโครงการ : </b><?php echo $row["Username"]; ?></p>
                  <p><b>ผู้ร่วมโครงการ : </b><?php echo $show_name_include; ?></p>
                  <p><b>ชื่อหน่วยงาน : </b><?php echo $row["Employer"]; ?></p>
                  <p><b>วันที่เริ่มโครงการ : </b><?php echo $row["Startdate"]; ?></p>
                  <p><b>วันที่สิ้นสุดโครงการ : </b><?php echo $row["Enddate"]; ?></p>
                  <p><b>ระยะเวลา(วัน) : </b><?php echo $days_between_set; ?></p>
                  <p><b>ความต้องการ : </b><?php echo $row["Requirement"]; ?></p>
                </div>
                <?php
              }
              ?>

              <!-- เอกสารเกี่ยวกับโครงกสร -->
              <!-- Fileupload select ///////////////////////////////////////////////////////////////////-->
              <p>
                <h3>เอกสารประกอบโครงการ</h3>
                <?php
                // to check if have no file in project
                $sql = "SELECT COUNT(File_id)
                FROM tb_fileupload
                WHERE (Project_id = $project_id)";
                $rs_num = mysqli_query($con, $sql);
                while ($row_num = mysqli_fetch_array($rs_num))
                if ($row_num[0] > 0) {
                ?>

                      <div class="table-responsive">
                      <p id="file_table">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>ชื่อเอกสาร</th>
                              <th>เพิ่มโดย</th>
                              <th>วันที่</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $sql_fileupload = "SELECT *
                            FROM tb_fileupload
                            WHERE (Project_id = $project_id AND Activity_id = 0)
                            ORDER BY File_id DESC";
                            $rs_fileupload = mysqli_query($con, $sql_fileupload);
                            while ($row_fileupload = mysqli_fetch_array($rs_fileupload)) { ?>
                            <tr>
                              <td>
                              <a href="fileupload/<?= $row_fileupload['Filename'] ?>"><?php echo $row_fileupload['Filename']; ?></a>
                              </td>
                              <td>
                              <?php
                              $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_fileupload['Username']}')";
                              $userprofile_rs = mysqli_query($con, $sql);
                              while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
                                  echo "<a href=\"userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a>";
                              }
                              ?>
                              </td>
                              <td>
                              <?php
                              echo $row_fileupload['Date'];
                              ?>
                              </td>
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </p>
                      </div>

                <?php } else { ?>
                <center><h4>ไม่มีเอกสารประกอบโครงการนี้</h4></center>
                <?php } ?>
                <!-- Fileupload select ///////////////////////////////////////////////////////////////////-->
              </p>

            <!-- แผนผังกำหนดการ grantt chart-->
            <?php
            $sql = "SELECT COUNT(Activity_id) FROM tb_activity WHERE (Project_id = $project_id)";
            $result = mysqli_query($con, $sql);
            while ($activity_num = mysqli_fetch_array($result)) {
            ?>
            <p>
              <h3>ตารางกิจกรรม</h3>
              <div id="Grantt" style="width: 100%; height: <?php echo (($activity_num[0]*35)+95); ?>px;"></div>
            </p>
            <?php }?>

            <!-- เอกสารเกี่ยวกับกิจกรรม -->
            <!-- Fileupload select ///////////////////////////////////////////////////////////////////-->
            <p>
              <h3>เอกสารประกอบกิจกรรม</h3>
              <?php
              // to check if have no file in project
              $sql = "SELECT COUNT(File_id)
              FROM tb_fileupload
              WHERE (Project_id = $project_id)";
              $rs_num = mysqli_query($con, $sql);
              while ($row_num = mysqli_fetch_array($rs_num))
              if ($row_num[0] > 0) {
              ?>

                    <div class="table-responsive">
                    <p id="file_table">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>ชื่อเอกสาร</th>
                            <th>เพิ่มโดย</th>
                            <th>วันที่</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $sql_fileupload = "SELECT *
                          FROM tb_fileupload
                          WHERE (Project_id = $project_id AND Activity_id != 0)
                          ORDER BY File_id DESC";
                          $rs_fileupload = mysqli_query($con, $sql_fileupload);
                          while ($row_fileupload = mysqli_fetch_array($rs_fileupload)) { ?>
                          <tr>
                            <td>
                            <a href="fileupload/<?= $row_fileupload['Filename'] ?>"><?php echo $row_fileupload['Filename']; ?></a>
                            </td>
                            <td>
                            <?php
                            $sql = "SELECT * FROM tb_user WHERE (Username = '{$row_fileupload['Username']}')";
                            $userprofile_rs = mysqli_query($con, $sql);
                            while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
                                echo "<a href=\"userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a>";
                            }
                            ?>
                            </td>
                            <td>
                            <?php
                            echo $row_fileupload['Date'];
                            ?>
                            </td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </p>
                    </div>

              <?php } else { ?>
              <center><h4>ไม่มีเอกสารประกอบกิจกรรมนี้</h4></center>
              <?php } ?>
              <!-- Fileupload select ///////////////////////////////////////////////////////////////////-->
            </p>

            <!-- แผนภูมิวงกลม -->
            <p>
              <h3>เวลาที่วางแผนไว้</h3>
              <div id="piechart" style="width: 100%; height: 300px;"></div>
            </p>

            <!-- แผนภูมิแท่ง -->
            <p>
              <h3>เวลาที่ใช้จริง</h3>
              <div id="columnchart_material" style="width: 100%; height: 300px;"></div>
            </p>

          </div> <!-- close print div -->

          <hr>
          <center>
            <p>
            <a href="index.php?page=<?= $page ?>" class="btn btn-default" role="button">กลับ</a>
          </p>
          </center>

        </div> <!-- close main container -->

        <?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>

      </body>
      </html>

<?php
mysqli_close($con);
?>
