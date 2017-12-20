<?php
session_start();

// connect to data base
require("../connect.php");

// get notification badges
$sql = "SELECT COUNT(*) FROM tb_notification WHERE ((Username = '{$_SESSION['user_login_name']}') AND (Status = 'Unread'))";
$rs = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($rs))
  if (($row[0] > 0) && ($row[0] < 99)) echo "&nbsp;<span class=\"label label-danger\">{$row[0]}</span>";
  elseif ($row[0] > 99) echo "&nbsp;<span class=\"label label-danger\">99+</span>";

  mysqli_close($con);
?>
