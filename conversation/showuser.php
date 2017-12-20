<div style="padding: 10px">

<?php
session_start();
require("../connect.php");

?>

<p>
<?php
if (isset($_SESSION['to_user'])) {
  $sql = "SELECT * FROM tb_user WHERE (Username = '{$_SESSION['to_user']}')";
  $result = mysqli_query($con, $sql);
  while ($row = mysqli_fetch_array($result)) {
    echo "ส่งข้อความถึง: ";
?>
<a href="../userprofile.php?username=<?php echo $row['Username']; ?>">
    <img src="../userpicture/<?php echo $row["Filename"]; ?>" class="img-circle" height="30" width="30">
<?php
    echo $row['Realname']."</a>";
  }
} else {
  echo "โปรดเลือกผู้รับข้อความ";
}
?>
</p>

</div>

<?php
mysqli_close($con);
?>
