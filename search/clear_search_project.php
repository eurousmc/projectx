<?php
// this file will clear session for search some file ;)
session_start();
unset($_SESSION['search_project']);
if (isset($_GET['msg'])) $msg = "?msg=".$_GET['msg'];
else $msg = "";
header("location: ../index.php$msg");
