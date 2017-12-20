<?php
// Check user login
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: permission/login.php");
if ($_SESSION['user_login_position'] == "Administrator") header("location: manage_user.php");

// connect to data base
require("connect.php");

$num_rec_per_page = 10;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page = 1; };

$start_from = ($page - 1) * $num_rec_per_page;

if (isset($_SESSION['search_project'])) $keyword = $_SESSION['search_project'];
else $keyword = "";

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
          <li><a href="search/clear_search_project.php"><span class="glyphicon">&#xe021;</span>&nbsp;หน้าแรก</a></li>
          <?php
          if ($_SESSION['user_login_position'] == "Staff") {
            echo '<li><a href="add/add_project.php"><span class="glyphicon">&#x2b;</span>&nbsp;สร้างโครงการ</a></li>';
          }
          ?>
        </ul>

        <div class="col-sm-3 col-md-3">
          <form method="post" action="search/check_search_project.php" class="navbar-form" role="search">
            <div class="form-group">
              <div class="input-group">
                <input type="text"
                class="form-control input-md"
                id="search_project"
                name="search_project"
                placeholder="ค้นหาโครงการ"
                value="<?php echo $keyword; ?>"
                >
                <div class="input-group-btn">
                  <button class="btn btn-default btn-md" type="submit">
                    <i class="glyphicon glyphicon-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </form> <!-- close form search -->
        </div>

        <ul class="nav navbar-nav navbar-right">
          <li><a href="userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
          <li><a href="conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
          <li><a href="search/clear_search_notification.php"><span class="glyphicon">&#xe123;</span>&nbsp;การแจ้งเตือน<span id="notification_badges"></span></a></li>
          <li><a href="permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
        </ul>

      </div>

    </div>
  </nav>

  <div class="container">
    <h3>หน้าแรก</h3>

<?php
// set Query to mysql
//-----------------------------------------------------------------------------------------------
if ($_SESSION['user_login_position'] == "Staff") {

  if ($keyword == Null || $keyword == "") {
    $sql_main = "SELECT *
    FROM tb_project
    WHERE (Project_id IN (SELECT Project_id FROM tb_activity WHERE (Username = '{$_SESSION['user_login_name']}')))
    OR (Username = '{$_SESSION['user_login_name']}')
    ORDER BY Project_id DESC
    ";
  } else {
    $sql_main = "SELECT *
    FROM tb_project
    WHERE ((
      (Project_id IN (SELECT Project_id FROM tb_activity WHERE (Username = '{$_SESSION['user_login_name']}')))
      OR (Username = '{$_SESSION['user_login_name']}')
    ) AND (
      Projectname LIKE '%$keyword%' OR
      Username LIKE '%$keyword%' OR
      Employer LIKE '%$keyword%'
    ))
    ORDER BY Project_id DESC
    ";
  }

} else {
  $sql_main = "SELECT *
  FROM tb_project
  WHERE (
    Projectname LIKE '%$keyword%' OR
    Username LIKE '%$keyword%' OR
    Employer LIKE '%$keyword%'
  )
  ORDER BY Project_id DESC
  ";
}
//-----------------------------------------------------------------------------------------------
?>

<!-- Show project list ##################################################################################### -->
<?php

$rs = mysqli_query($con, $sql_main." LIMIT $start_from, $num_rec_per_page");

if (mysqli_num_rows($rs) > 0) {
?>

<div class="table-responsive">
<table class="table table-striped">
  <thead>
    <tr>
      <th>ชื่อโครงการ</th>
      <th>ผู้รับผิดชอบโครงการ</th>
      <th>ความคืบหน้า</th>
    </tr>
  </thead>
  <tbody>

<?php
  while ($row = mysqli_fetch_array($rs)) {
    // get project process percent *********************************************
    $sql = "SELECT *
    FROM tb_activity
    WHERE (Project_id = {$row['Project_id']})";
    $activity_status_rs = mysqli_query($con, $sql);
    $num_finished = 0;
    while ($activity_status_row = mysqli_fetch_array($activity_status_rs)) {
      if ($activity_status_row['Status'] == "Finish") $num_finished++;
      elseif ($activity_status_row['Status'] == "Process") $num_finished = $num_finished + 0.5;
    }
    $activity_status = round(($num_finished * 90) / mysqli_num_rows($activity_status_rs)) + 10;

    // print data
    echo "<tr>
    <td><a href=\"project_overview.php?project_id={$row['Project_id']}&page=$page\" title=\"กดเพื่อดูรายละเอียดโครงการ\">{$row[1]}</a></td>";

    $sql = "SELECT * FROM tb_user WHERE (Username = '{$row['Username']}')";
    $userprofile_rs = mysqli_query($con, $sql);
    while ($userprofile_row = mysqli_fetch_array($userprofile_rs)) {
      if ($userprofile_row['Username'] == $_SESSION['user_login_name'])
        echo "<td><a href=\"userprofile.php?username={$userprofile_row[0]}\">คุณ</a></td>";
      else
        echo "<td><a href=\"userprofile.php?username={$userprofile_row[0]}\">{$userprofile_row[2]}</a></td>";
    }

    echo "<td>
    <div class=\"progress\"><div class=\"progress-bar\" role=\"progressbar\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: $activity_status%\">
    $activity_status%</div></div>
    </td>";

    echo "</tr>";
  } // close while

?>
    </tbody>
  </table>

</div> <!-- close class table responsive -->

<?php if ($keyword != "") { ?>
  <center>
    <a href="search/clear_search_project.php" class="btn btn-default">กลับ</a>
  </center>
  <?php } ?>

<p> <!-- to create page number ************************************************* -->
  <center>
    <?php
    $rs_result = mysqli_query($con, $sql_main); //run the query

    $total_records = mysqli_num_rows($rs_result);  //count number of records
    $total_pages = ceil($total_records / $num_rec_per_page);

    if ($total_records > $num_rec_per_page) {

      echo "<ul class=\"pagination\">";
      echo "<li><a href='index.php?page=1'>|<</a></li>"; // Goto 1st page

      if ($page != 1)
      echo "<li><a href='index.php?page=".($page - 1)."'><</a></li>"; // Goto page - 1

      for ($i = 1; $i <= $total_pages; $i++) {
        if ($page == $i) {
          echo "<li class=\"active\"><a href=\"index.php?page=$i\">$i</a></li>";
        } else {
          echo "<li><a href=\"index.php?page=$i\">$i</a></li>";
        }
      }

      if ($page != $total_pages)
      echo "<li><a href='index.php?page=".($page + 1)."'>></a></li>"; // Goto page + 1

      echo "<li><a href='index.php?page=$total_pages'>".'>|'."</a></li>"; // Goto last page
      echo "</ul>";
    }
    ?>
  </center>
</p>

<?php
} else {
  echo "<div class=\"container\"><center><h4>ไม่พบโครงการที่ค้นหา</h4></center></div>";
  echo "<center><p><a href=\"search/clear_search_project.php\" class=\"btn btn-default\" role=\"button\">กลับ</a></p></center>";
}
?>

<!-- ##################################################################################### -->

  </div> <!-- close container -->

<?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>

</body>
</html>

<?php
// close connection from database
mysqli_close($con);
?>
