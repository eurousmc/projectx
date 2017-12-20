<?php
// Check user login
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Administrator")) header("location: permission/login.php");

// connect to data base
require("connect.php");

// clear session in add user page
unset($_SESSION['new_username']);
unset($_SESSION['new_password']);
unset($_SESSION['new_confirm_password']);
unset($_SESSION['new_realname']);
unset($_SESSION['new_position']);

$num_rec_per_page = 10;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page = 1; };

$start_from = ($page - 1) * $num_rec_per_page;

if (isset($_SESSION['search_user'])) $keyword = $_SESSION['search_user'];
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
        <li><a href="search/clear_search_user.php"><span class="glyphicon">&#xe021;</span>&nbsp;หน้าแรก</a></li>
        <li><a href="add/add_user.php?page=<?= $page ?>"><span class="glyphicon">&#x2b;</span>&nbsp;เพิ่มบัญชีผู้ใช้งาน</a></li>
      </ul>
      <div class="col-sm-3 col-md-3">
        <!--##### Search here #####-->
        <form action="search/check_search_user.php" method="post" class="navbar-form" role="search">
          <div class="form-group">
            <div class="input-group">
              <input type="text"
              class="form-control"
              id="search_user"
              name="search_user"
              placeholder="ค้นหาบัญชีผู้ใช้งาน"
              value="<?= $keyword ?>"
              >
              <div class="input-group-btn">
                <button class="btn btn-default" type="submit">
                  <i class="glyphicon glyphicon-search"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="userprofile.php?username=<?php echo $_SESSION['user_login_name']; ?>"><img src="userpicture/<?php echo $_SESSION['user_login_picture'] ?>" height="25" width="25">&nbsp;<?php echo $_SESSION['user_login_realname']; ?></a></li>
        <li><a href="conversation/chatroom.php"><span class="glyphicon">&#xe111;</span>&nbsp;ข้อความ<span id="message_badges"></span></a></li>
        <li><a href="permission/logout.php"><span class="glyphicon">&#xe163;</span>&nbsp;ออกจากระบบ</a></li>
      </ul>
    </div>
  </div>
  </nav>

  <div class="container">
      <h3>จัดการบัญชีผู้ใช้</h3>

      <div class="container">

        <?php
        // set Query to mysql
        //-----------------------------------------------------------------------------------------------

        if ($keyword == Null || $keyword == "") {
          $sql_main = "SELECT *
          FROM tb_user
          ";
        } else {
          $sql_main = "SELECT *
          FROM tb_user
          WHERE (
          Username LIKE '%$keyword%' OR
          Realname LIKE '%$keyword%'
          )
          ";
        }

        //-----------------------------------------------------------------------------------------------

        $rs = mysqli_query($con, $sql_main." LIMIT $start_from, $num_rec_per_page");
        if (mysqli_num_rows($rs) > 0) {
        ?>

        <p id="data_table">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>ชื่อบัญชีผู้ใช้งาน</th>
                <th>รหัสผ่าน</th>
                <th>ชื่อผู้ใช้งาน</th>
                <th>ตำแหน่ง</th>
                <th>แก้ไข</th>
                <th>ลบ</th>
              </tr>
            </thead>
            <tbody>
              <?php

              while ($row = mysqli_fetch_array($rs)) {

                if ($row[3] == "Administrator") {
                  $str_position = "ผู้ดูแลระบบ";
                } else if ($row[3] == "Manager") {
                  $str_position = "ผู้บริหาร";
                } else {
                  $str_position = "ผู้ปฏิบัติงาน";
                }

                echo "<tr>
                <td><img src=\"userpicture/{$row[4]}\" height=\"25\" width=\"25\">&nbsp;{$row[0]}</td>
                <td>{$row[1]}</td>
                <td>{$row[2]}</td>
                <td>$str_position</td>
                <td>
                <a href=\"edit/edit_user.php?username={$row[0]}&page=$page\" class=\"btn btn-default btn-sm\" role=\"button\">
                <span class=\"glyphicon glyphicon-edit\"></span>
                </a>
                </td>
                <td>
                <a href=\"delete\delete_user.php?username={$row[0]}&page=$page\" class=\"btn btn-default btn-sm\" role=\"button\">
                <span class=\"glyphicon glyphicon-trash\"></span>
                </a>
                </td>
              </tr>";
            }
            ?>
          </tbody>
        </table>

        <?php if ($keyword != "") { ?>
        <center>
          <a href="search/clear_search_user.php" class="btn btn-default">กลับ</a>
        </center>
        <?php } ?>

      </p>
    <?php } else { ?>
      <div class="container"><center>
        <h4>ไม่พบการแจ้งเตือนที่ค้นหา</h4>
        <a href="search/clear_search_user.php" class="btn btn-default">กลับ</a>
      </center></div>
    <?php } //close if (mysqli_num_rows($rs) > 0) { ?>
    </div>

<!-- to create page number ************************************************* -->
    <p>
      <center>
        <?php
        $rs_result = mysqli_query($con, $sql_main); //run the query

        $total_records = mysqli_num_rows($rs_result);  //count number of records
        $total_pages = ceil($total_records / $num_rec_per_page);

        if ($total_records > $num_rec_per_page) {

          echo "<ul class=\"pagination\">";
          echo "<li><a href='manage_user.php?page=1'>|<</a></li>"; // Goto 1st page

          if ($page != 1)
          echo "<li><a href='manage_user.php?page=".($page - 1)."'><</a></li>"; // Goto page - 1

          for ($i = 1; $i <= $total_pages; $i++) {

            if ($page == $i) {
              echo "<li class=\"active\"><a href=\"notification.php?page=$i\">$i</a></li>";
            } else {
              echo "<li><a href=\"manage_user.php?page=$i\">$i</a></li>";
            }

          }

          if ($page != $total_pages)
          echo "<li><a href='manage_user.php?page=".($page + 1)."'>></a></li>"; // Goto page + 1

          echo "<li><a href='manage_user.php?page=$total_pages'>".'>|'."</a></li>"; // Goto last page
          echo "</ul>";
        }
        ?>
      </center>
    </p>
<!-- *********************************************************************** -->

  </div> <!-- close main container -->


</body>
</html>

<?php if (isset($_GET['msg'])) echo "<script>alert(\"".$_GET['msg']."\");</script>"; ?>

<?php
// close connection from database
mysqli_close($con);
?>
