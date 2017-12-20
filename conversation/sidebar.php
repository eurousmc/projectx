<?php
session_start();

// connect to data base
require("../connect.php");

// จาก table tb_user
$sql = "SELECT * FROM tb_user";
$result = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($result)) {
  if ($row["Username"] != $_SESSION['user_login_name']) { ?>
    <!--แสดงผลรูปภาพ กับ ชื่อผู้ใช้งาน -->

    <button type="image" onclick="call_user_session('<?php echo $row['Username']?>')">
      <img src="../userpicture/<?php echo $row["Filename"] ?>" class="img-circle" height="30" width="30">
      <?php
      echo $row["Realname"];
      // get message badges
      $sql = "SELECT COUNT(*) FROM tb_conversation WHERE ((To_user = '{$_SESSION['user_login_name']}' AND From_user = '{$row['Username']}') AND (Status = 'Unread'))";
      $rs_badges = mysqli_query($con, $sql);
      while ($row_badges = mysqli_fetch_array($rs_badges))
      if (($row_badges[0] > 0) && ($row_badges[0] < 99)) echo "&nbsp;<span class=\"label label-danger\">{$row_badges[0]}</span>";
      elseif ($row_badges[0] > 99) echo "&nbsp;<span class=\"label label-danger\">99+</span>";
      echo "</button></br>";



    }
  }
  ?>

  <script>
  function call_user_session(name){
    $(document).ready(function(){
      $.ajax({
        type: 'post',
        url: 'to_user_session.php',
        data: { Username : name }
      });
    });
  }
  </script>

<?php
mysqli_close($con);
?>
