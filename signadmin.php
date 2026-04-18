<?php
include_once 'dbConnection.php';
$ref=@$_GET['q'];
//$name = $_POST['name'];
//$name= ucwords(strtolower($name));
//$gender = $_POST['gender'];
$email = trim($_POST['email']);
$password = trim($_POST['password']);

$q=mysqli_query($con,"INSERT INTO admin VALUES  ('$email' , '$password' , 'admin')");

// OLD (broken & no message):
// header("location:$ref?q=Succesfully registered");

// NEW: send user back to Head dashboard with a clear success flag
header("Location: headdash.php?q=4&msg=teacher_added");
exit();
?>
