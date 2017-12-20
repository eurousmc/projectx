<?php
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "std57126db";

$con = mysqli_connect($db_host, $db_user, $db_password, $db_name) or die("ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้");

mysqli_query($con, "SET NAMES UTF8");
//mysqli_query($con, "SET character_set_results=utf8");
//mysqli_query($con, "SET character_set_client=utf8");
//mysqli_query($con, "SET character_set_connection=utf8");
