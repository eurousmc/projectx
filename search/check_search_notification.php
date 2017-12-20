<?php
// Check user login permission
session_start();
if (!isset($_SESSION['user_login_position'])) header("location: ../permission/login.php");

// create search_project session
$_SESSION['search_notification'] = $_POST['search_notification'];

// then go to index.php
header("location: ../notification.php");
