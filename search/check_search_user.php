<?php
// Check user login permission
session_start();
if ((!isset($_SESSION['user_login_position'])) || ($_SESSION['user_login_position'] != "Administrator")) header("location: ../permission/login.php");

// create search_project session
$_SESSION['search_user'] = $_POST['search_user'];

// then go to index.php
header("location: ../manage_user.php");
