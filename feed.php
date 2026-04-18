<?php
include_once 'dbConnection.php';
session_start();

// ✅ Allow viewing feedback if a valid feedback ID is provided (even if not logged in)
if (!isset($_SESSION['email']) && !isset($_GET['fid'])) {
    echo "<h3 style='color:red;text-align:center;'>Access denied!</h3>";
    exit();
}

if (!isset($_GET['fid'])) {
    echo "<h3 style='color:red;text-align:center;'>No feedback ID provided!</h3>";
    exit();
}

$fid = mysqli_real_escape_string($con, $_GET['fid']);
$result = mysqli_query($con, "SELECT * FROM feedback WHERE id='$fid' LIMIT 1");

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<h3 style='color:red;text-align:center;'>Feedback not found!</h3>";
    exit();
}

$row = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Feedback</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
body { background:#f4f6f9; font-family:Arial, sans-serif; padding:40px; }
.panel { background:white; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); padding:25px; max-width:700px; margin:auto; }
h3 { color:#673AB7; margin-bottom:20px; }
p { font-size:16px; }
</style>
</head>
<body>
<div class="panel">
  <h3>💬 Feedback Details</h3>
  <p><strong>From:</strong> <?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['email']); ?>)</p>
  <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
  <p><strong>Date:</strong> <?php echo htmlspecialchars($row['date']); ?>, <strong>Time:</strong> <?php echo htmlspecialchars($row['time']); ?></p>
  <hr>
  <p><strong>Feedback:</strong></p>
  <p style="white-space:pre-line;"><?php echo nl2br(htmlspecialchars($row['feedback'])); ?></p>
</div>
</body>
</html>
