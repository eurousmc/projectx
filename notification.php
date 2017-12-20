<?php
// Check user login
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: permission/login.php");

// connect to data base
require("connect.php");

$num_rec_per_page = 10;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page = 1; };

$start_from = ($page - 1) * $num_rec_per_page;

if (isset($_SESSION['search_notification'])) $keyword = $_SESSION['search_notification'];
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
          <li><a href="index.php"><span class="glyphicon">&#xe021;</span>&nbsp;หน้าแรก</a></li>
        </ul>

        <div class="col-sm-3 col-md-3">
          <form method="post" action="search/check_search_notification.php" class="navbar-form" role="search">
            <div class="form-group">
              <div class="input-group">
                <input type="text"
                class="form-control input-md"
                id="search_notification"
                name="search_notification"
                placeholder="ค้นหาการแจ้งเตื่อน"
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
    <h3>การแจ้งเตือน</h3>

    <?php
    // set Query to mysql
    //-----------------------------------------------------------------------------------------------

    if ($keyword == Null || $keyword == "") {
      $sql_main = "SELECT *
      FROM tb_notification
      WHERE (Username = '{$_SESSION['user_login_name']}')
      ORDER BY Notification_id DESC
      ";
    } else {
      $sql_main = "SELECT *
      FROM tb_notification
      WHERE ((Username = '{$_SESSION['user_login_name']}'
      ) AND (
      Message LIKE '%$keyword%' OR
      Date LIKE '%$keyword%' OR
      Time LIKE '%$keyword%'
      ))
      ORDER BY Notification_id DESC
      ";
    }

//-----------------------------------------------------------------------------------------------
?>

<!-- Show activity list ##################################################################################### -->
<?php
$rs = mysqli_query($con, $sql_main." LIMIT $start_from, $num_rec_per_page");

if (mysqli_num_rows($rs) > 0) {
?>

<div class="container">

      <?php
      // print data
      while ($row = mysqli_fetch_array($rs)) {
        // project ID = 0 is delete

        // get user profile picture
        $sql = "SELECT * FROM tb_user WHERE (Username = '{$row['From_username']}')";
        $rs_pic = mysqli_query($con, $sql);
        while ($row_pic = mysqli_fetch_array($rs_pic)) $user_pic = $row_pic['Filename'];

      ?>

      <div class="row">
        <div class="col-md-12">
          <?php if ($row['Project_id'] != 0) { ?>
          <div class="alert alert-info">
            <table>
              <tr>
                <td>
                  <img src="userpicture/<?= $user_pic ?>" height="25" width="25">&nbsp;
                </td>
                <td>
                  <a href="project_overview.php?project_id=<?= $row['Project_id'] ?>">
                    <b><?= $row['Message'] ?></b><br>
                    <?php if (date('Y-m-d') == $row['Date']) echo "วันนี้"; else echo $row['Date']; ?>&nbsp;
                    <?= $row['Time'] ?>
                  </a>
                </td>
              </tr>
            </table>
          </div>
          <?php } else { ?>
          <div class="alert alert-danger">
            <table>
              <tr>
                <td>
                  <img src="userpicture/<?= $user_pic ?>" height="25" width="25">&nbsp;
                </td>
                <td>
                  <b><?= $row['Message'] ?></b><br>
                  <?php if (date('Y-m-d') == $row['Date']) echo "วันนี้"; else echo $row['Date']; ?>&nbsp;
                  <?= $row['Time'] ?>
                </td>
              </tr>
            </table>
          </div>
          <?php } ?>
        </div>
      </div>

      <?php
      } // close while
      ?>

</div> <!-- close class table responsive -->

<p> <!-- to create page number ************************************************* -->
  <center>
    <?php
    $rs_result = mysqli_query($con, $sql_main); //run the query

    $total_records = mysqli_num_rows($rs_result);  //count number of records
    $total_pages = ceil($total_records / $num_rec_per_page);

    if ($total_records > $num_rec_per_page) {

      echo "<ul class=\"pagination\">";
      echo "<li><a href='notification.php?page=1'>|<</a></li>"; // Goto 1st page

      if ($page != 1)
      echo "<li><a href='notification.php?page=".($page - 1)."'><</a></li>"; // Goto page - 1

      for ($i = 1; $i <= $total_pages; $i++) {

        if ($page == $i) {
          echo "<li class=\"active\"><a href=\"notification.php?page=$i\">$i</a></li>";
        } else {
          echo "<li><a href=\"notification.php?page=$i\">$i</a></li>";
        }

      }

      if ($page != $total_pages)
      echo "<li><a href='notification.php?page=".($page + 1)."'>></a></li>"; // Goto page + 1

      echo "<li><a href='notification.php?page=$total_pages'>".'>|'."</a></li>"; // Goto last page
      echo "</ul>";
    }
    ?>
  </center>
</p>

<?php
} else {
  echo "<div class=\"container\"><center><h4>ไม่พบการแจ้งเตือนที่ค้นหา</h4></center></div>";
} //close if (mysqli_num_rows($rs) > 0) {

?>
<!-- ##################################################################################### -->

<center>
  <p>
    <a href="index.php" class="btn btn-default" role="button">กลับ</a>
  </p>
</center>

</div> <!-- close main container -->

</body>
</html>

<?php

// to set all status to read
$sql = "UPDATE tb_notification
SET Status = 'Read'
WHERE ((Username = '{$_SESSION['user_login_name']}') AND (Status = 'Unread'))";
mysqli_query($con, $sql);

// close connection from database
mysqli_close($con);
?>
