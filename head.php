<?php
include_once 'dbConnection.php';
$ref = @$_GET['q'];
$email = trim($_POST['uname']);
$password = md5($_POST['password']); // hashed password

// Use full selection to check admin details
$query = "SELECT * FROM admin WHERE email='$email' AND password='$password' AND LOWER(role)='head'";
$result = mysqli_query($con, $query) or die('Database query error: ' . mysqli_error($con));

if (mysqli_num_rows($result) == 1) {
    session_start();
    if (isset($_SESSION['email'])) {
        session_unset();
    }
    $_SESSION["name"] = 'Admin';
    $_SESSION["key"] = 'tarun123';
    $_SESSION["email"] = $email;
    header("Location: headdash.php?q=0");
    exit();
} else {
    header("Location: $ref?w=Warning : Access denied");
    exit();
}
?>
