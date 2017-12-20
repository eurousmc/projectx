<?php
session_start();
require("../connect.php");

if ((isset($_POST['newmessage'])) && ($_POST['newmessage'] != "") && (isset($_SESSION['to_user']))) {

  $from_user = $_SESSION['user_login_name'];
  $to_user = $_SESSION['to_user'];
  $message = mysqli_real_escape_string($con, $_POST['newmessage']);
  $date = Date('Y-m-d');
  $time = Date('H:i');

  $sql = "INSERT INTO tb_conversation
  VALUES (
    NULL,
    '$from_user',
    '$to_user',
    '$message',
    '$date',
    '$time',
    'Unread'
  )";
  mysqli_query($con, $sql);
}

mysqli_close($con);
?>
