<?php
include_once 'dbConnection.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("location:index.php");
    exit();
}
$email = $_SESSION['email'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Online Examiner | Head Dashboard</title>

<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/bootstrap-theme.min.css"/>    
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/font.css">
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,700,500" rel="stylesheet">

<style>
body {
  background: #f4f6f9;
  font-family: 'Roboto', sans-serif;
}
.navbar {
  background: linear-gradient(135deg, #673AB7, #512DA8);
  border: none;
  border-radius: 0;
}
.navbar-brand, .navbar-nav li a {
  color: white !important;
  font-weight: 500;
}
.navbar-nav li.active a {
  background: rgba(255,255,255,0.2) !important;
  color: #ffffffff !important;
}
.panel {
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  border-radius: 10px;
  padding: 25px;
}
.table thead {
  background: linear-gradient(135deg, #673AB7, #512DA8);
  color: white;
}
.btn-primary {
  background: #673AB7;
  border: none;
  border-radius: 6px;
}
.btn-primary:hover {
  background: #512DA8;
}
h2.title {
  color: #673AB7;
  font-weight: 700;
}
.gold-row { background-color: #d27f58ff; }
.silver-row { background-color: #2e6eaeff; }
.bronze-row { background-color: #0cd358ff; }

/* NEW: make dropdown items visible */
.navbar .dropdown-menu > li > a {
  color: #333 !important;
}
.navbar .dropdown-menu > li > a:hover,
.navbar .dropdown-menu > li > a:focus {
  background: #673AB7;
  color: #fff !important;
}
</style>

</head>

<body>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="headdash.php?q=0"><b>Dashboard - Head</b></a>
    </div>
    <ul class="nav navbar-nav">
      <li <?php if(@$_GET['q']==0) echo'class="active"'; ?>><a href="headdash.php?q=0">🏠 Home</a></li>
      <li <?php if(@$_GET['q']==1) echo'class="active"'; ?>><a href="headdash.php?q=1">👥 Users</a></li>
      <li <?php if(@$_GET['q']==2) echo'class="active"'; ?>><a href="headdash.php?q=2">🏆 Ranking</a></li>
      <li <?php if(@$_GET['q']==3) echo'class="active"'; ?>><a href="headdash.php?q=3">💬 Feedback</a></li>
      <li class="dropdown <?php if(@$_GET['q']==4 || @$_GET['q']==5) echo'active'; ?>">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">👩‍🏫 Teacher <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="headdash.php?q=4">Add Teacher</a></li>
          <li><a href="headdash.php?q=5">Remove Teacher</a></li>
          <li><a href="generate_ai_questions.php">AI Question Generator 🤖</a></li>
        </ul>
      </li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $email; ?></a></li>
      <li><a href="logout.php?q=headdash.php"><span class="glyphicon glyphicon-log-out"></span> Sign out</a></li>
    </ul>
  </div>
</nav>

<div class="container">

<?php
// NEW: flash messages for teacher actions
if (isset($_GET['msg'])) {
    $msg  = $_GET['msg'];
    $type = 'info';
    $text = '';

    if ($msg === 'teacher_added') {
        $type = 'success';
        $text = 'Teacher added successfully.';
    } elseif ($msg === 'teacher_removed') {
        $type = 'danger';
        $text = 'Teacher removed successfully.';
    } elseif ($msg === 'teacher_removed_quizzes') {
        $type = 'warning';
        $text = 'Teacher and all their quizzes removed.';
    }

    if ($text !== '') {
        echo '
        <div class="alert alert-' . $type . ' alert-dismissible" role="alert" style="margin-top:15px;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          ' . htmlspecialchars($text, ENT_QUOTES, "UTF-8") . '
        </div>';
    }
}
?>

<div class="row">
<div class="col-md-12">

<!-- 🏠 Home -->

<?php if(@$_GET['q']==0) {
$result = mysqli_query($con,"SELECT * FROM quiz ORDER BY date DESC");
echo '<div class="panel"><h2 class="title">📘 All Quizzes</h2>
<table class="table table-striped">
<thead><tr><th>S.N.</th><th>Topic</th><th>Total Questions</th><th>Marks</th><th>+ve</th><th>-ve</th><th>Time Limit</th></tr></thead><tbody>';
$c=1;
while($row=mysqli_fetch_array($result)) {
  echo '<tr><td>'.$c++.'</td><td>'.$row['title'].'</td><td>'.$row['total'].'</td>
  <td>'.$row['sahi']*$row['total'].'</td><td>'.$row['sahi'].'</td><td>'.$row['wrong'].'</td><td>'.$row['time'].' min</td></tr>';
}
echo '</tbody></table></div>';
} ?>

<!-- 🏆 Dynamic Ranking Section -->

<?php if(@$_GET['q']==2) { ?>

<div class="panel">
  <h2 class="title">🏆 Quiz Rankings</h2>
  <div class="form-group">
    <label>Select Quiz:</label>
    <select id="quizSelect" class="form-control" style="max-width:300px;">
      <option value="">-- Choose a Quiz --</option>
      <?php
      $quizQuery = mysqli_query($con, "SELECT eid, title FROM quiz ORDER BY date DESC");
      while ($quiz = mysqli_fetch_assoc($quizQuery)) {
          echo '<option value="'.$quiz['eid'].'">'.$quiz['title'].'</option>';
      }
      ?>
    </select>
  </div>
  <div id="rankingTable" style="margin-top:20px;">
    <p>Select a quiz to view rankings.</p>
  </div>
</div>

<script>
$('#quizSelect').change(function() {
  let eid = $(this).val();
  if (!eid) return;
  $('#rankingTable').html('<p>Loading rankings...</p>');

  $.getJSON('fetch_ranking.php?eid=' + eid, function(data) {
    if (data.error) {
      $('#rankingTable').html('<p style="color:red;">' + data.error + '</p>');
      return;
    }

    let html = `
      <table class="table table-striped">
        <thead><tr><th>Rank</th><th>Name</th><th>Gender</th><th>College</th><th>Score</th></tr></thead><tbody>
    `;

    if (data.length === 0) {
      html += '<tr><td colspan="5">No results yet for this quiz.</td></tr>';
    } else {
      data.forEach((row, i) => {
        let medal = '', rowClass = '';
        if (i === 0) { medal = '🥇'; rowClass = 'gold-row'; }
        else if (i === 1) { medal = '🥈'; rowClass = 'silver-row'; }
        else if (i === 2) { medal = '🥉'; rowClass = 'bronze-row'; }

        html += `<tr class="${rowClass}">
          <td>${medal || row.rank}</td>
          <td>${row.name}</td>
          <td>${row.gender}</td>
          <td>${row.college}</td>
          <td><b>${row.score}</b></td>
        </tr>`;
      });
    }
    html += '</tbody></table>';
    $('#rankingTable').html(html);
  });
});
</script>

<?php } ?>

<!-- 👥 Users -->

<?php if(@$_GET['q']==1) {
$result=mysqli_query($con,"SELECT * FROM user");
echo '<div class="panel"><h2 class="title">👥 Registered Users</h2>
<table class="table table-striped"><thead><tr><th>S.N.</th><th>Name</th><th>Gender</th><th>College</th><th>Email</th><th>Mobile</th><th></th></tr></thead><tbody>';
$c=1;
while($row=mysqli_fetch_array($result)) {
  echo '<tr><td>'.$c++.'</td><td>'.$row['name'].'</td><td>'.$row['gender'].'</td><td>'.$row['college'].'</td><td>'.$row['email'].'</td><td>'.$row['mob'].'</td>
  <td><a href="update.php?demail='.$row['email'].'" title="Delete User"><span class="glyphicon glyphicon-trash"></span></a></td></tr>';
}
echo '</tbody></table></div>';
} ?>

<!-- 💬 Feedback -->

<?php if(@$_GET['q']==3) {
$result=mysqli_query($con,"SELECT * FROM feedback ORDER BY date DESC");
echo '<div class="panel"><h2 class="title">💬 Feedback</h2>
<table class="table table-striped"><thead><tr><th>S.N.</th><th>Subject</th><th>Email</th><th>Date</th><th>Time</th><th>Name</th><th></th><th></th></tr></thead><tbody>';
$c=1;
while($row=mysqli_fetch_array($result)) {
  $date=date("d-m-Y",strtotime($row['date']));
  echo '<tr><td>'.$c++.'</td><td>'.$row['subject'].'</td><td>'.$row['email'].'</td><td>'.$date.'</td><td>'.$row['time'].'</td><td>'.$row['name'].'</td>
  <td>
  <a href="feed.php?fid=' . $row["id"] . '" target="_blank" title="View Feedback">
    <span class="glyphicon glyphicon-folder-open"></span>
  </a>
</td>




  <td><a href="update.php?fdid='.$row['id'].'"><span class="glyphicon glyphicon-trash"></span></a></td></tr>';
}
echo '</tbody></table></div>';
} ?>

<!-- ➕ Add Teacher -->

<?php if(@$_GET['q']==4) {
echo '<div class="panel"><h2 class="title">➕ Add Teacher</h2>
<form class="form-horizontal" action="signadmin.php?q=headdash.php?q=4" method="POST">
<div class="form-group"><input type="email" name="email" placeholder="Teacher Email" class="form-control" required></div>
<div class="form-group"><input type="password" name="password" placeholder="Password" class="form-control" required></div>
<div class="form-group"><button type="submit" class="btn btn-primary">Add Teacher</button></div>
</form></div>';
} ?>

<!-- 🗑️ Remove Teacher -->

<?php if(@$_GET['q']==5) {
$result=mysqli_query($con,"SELECT * FROM admin WHERE role='admin'");
echo '<div class="panel"><h2 class="title">🗑️ Remove Teacher</h2>
<table class="table table-striped">
<thead><tr><th>Email</th><th>Action</th></tr></thead><tbody>';
while($row=mysqli_fetch_array($result)) {
  echo '<tr>
          <td>'.$row['email'].'</td>
          <td>
            <button class="btn btn-danger btn-sm" 
                    data-toggle="modal" 
                    data-target="#removeTeacherModal" 
                    data-email="'.$row['email'].'">
              <span class="glyphicon glyphicon-trash"></span> Remove
            </button>
          </td>
        </tr>';
}
echo '</tbody></table></div>';
} ?>

<!-- 🧹 Remove Teacher Modal -->
<div class="modal fade" id="removeTeacherModal" tabindex="-1" role="dialog" aria-labelledby="removeTeacherLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#673AB7;color:white;">
        <h4 class="modal-title" id="removeTeacherLabel">Remove Teacher</h4>
      </div>
      <div class="modal-body">
        <p>Do you also want to remove all quizzes created by this teacher?</p>
        <input type="hidden" id="teacherEmail">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="removeOnlyTeacher">Remove Only Teacher</button>
        <button type="button" class="btn btn-warning" id="removeWithQuizzes">Remove Teacher & All Quizzes</button>
      </div>
    </div>
  </div>
</div>

<script>
// Capture teacher email when modal opens
$('#removeTeacherModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var email = button.data('email');
  $('#teacherEmail').val(email);
});

// Handle the button clicks
$('#removeOnlyTeacher').click(function() {
  var email = $('#teacherEmail').val();
  window.location = "update.php?demail1=" + encodeURIComponent(email);
});

$('#removeWithQuizzes').click(function() {
  var email = $('#teacherEmail').val();
  window.location = "update.php?demail1=" + encodeURIComponent(email) + "&remove_quizzes=yes";
});
</script>


</div>
</div>
</div>
</body>
</html>
