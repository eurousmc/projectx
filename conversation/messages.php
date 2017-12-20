<div style="padding: 10px">

<?php
session_start();
require("../connect.php");
?>

<p>
<div class="container">

  <?php

  if (isset($_SESSION['to_user'])) {

    $to_user = $_SESSION['to_user'];
    $from_user = $_SESSION['user_login_name'];

    // get user picture
    $sql = "SELECT * FROM tb_user WHERE (Username = '$to_user')";
    $rs_pic = mysqli_query($con, $sql);
    while ($row_pic = mysqli_fetch_array($rs_pic)) {
      $from_user_pic = $row_pic['Filename'];
      $from_user_realname = $row_pic['Realname'];
    }
    $to_user_pic = $_SESSION['user_login_picture'];
    $to_user_realname = $_SESSION['user_login_realname'];

    $sql = "SELECT *
    FROM tb_conversation
    WHERE (
      (From_user = '$from_user' AND To_user = '$to_user')
      OR
      (From_user = '$to_user' AND To_user = '$from_user')
    )
    ORDER BY Message_id DESC;
    ";

    $rs = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($rs)) {
      ?>
      <div class="row">
        <div class="col-sm-12">
          <div style="margin: 10px; padding: 5px; border: solid black 2px; border-radius: 5px; text-align: left;">

        <?php if ($row['From_user'] == $from_user) { ?>
          <div style="text-align: left;"><img src="../userpicture/<?= $to_user_pic ?>" class="img-circle" height="30" width="30">&nbsp;<b><?= $to_user_realname ?>:</b><br>
        <?php } else { ?>
          <div style="text-align: left;"><img src="../userpicture/<?= $from_user_pic ?>" class="img-circle" height="30" width="30">&nbsp;<b><?= $from_user_realname ?>:</b><br>
        <?php }
        echo $row['Message'];
        echo "<br>".$row['Date']."&nbsp;";
        echo $row['Time'];
        if (($row['From_user'] == $from_user) && ($row['Status'] == "Read")) echo "&nbsp;<span class=\"glyphicon\">&#xe013;</span>";
        ?>
          <br>
          </div>
        </div>
      </div>
    </div>
    <?php
  }

  // set status to read if user come to this page
  $sql = "UPDATE tb_conversation
  SET Status = 'Read'
  WHERE (From_user = '$to_user' AND To_user = '$from_user')";
  mysqli_query($con, $sql);
}

?>
</div>
</p>

<?php
mysqli_close($con);
?>

</div>
