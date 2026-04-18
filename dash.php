<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Online Examiner - Teacher Dashboard</title>

<link rel="stylesheet" href="css/bootstrap.min.css"/>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<style>
body {
  background: #f2f3f7;
  font-family: 'Segoe UI', sans-serif;
  color: #333;
  margin: 0;
}
.navbar {
  background: linear-gradient(90deg, #3f51b5, #673ab7);
  border: none;
  border-radius: 0;
  padding: 10px 0;
}
.navbar-brand, .navbar-nav > li > a {
  color: #fff !important;
  font-weight: 500;
  font-size: 16px;
}
.navbar .active > a,
.navbar-nav > li > a:hover {
  background-color: rgba(255,255,255,0.15) !important;
  color: #fff !important;
}
.dropdown-menu {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.dropdown-menu li a:hover {
  background: #ede7f6 !important;
  color: #4a148c !important;
}
.panel {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  padding: 25px;
  margin-top: 25px;
}
h3 {
  color: #3f51b5;
  font-weight: 600;
}
.table th {
  background: #d1c4e9;
  color: #311b92;
  text-align: center;
}
.table td { text-align: center; }
.btn-purple {
  background: #5e35b1;
  color: #fff;
}
.btn-purple:hover {
  background: #4527a0;
}
fieldset {
  border: 2px solid #d1c4e9;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
}
legend {
  color: #4527a0;
  font-weight: bold;
}
</style>
</head>

<body>
<?php
include_once 'dbConnection.php';
session_start();
if(!isset($_SESSION['email'])){
    header("location:index.php");
    exit();
}
$email = $_SESSION['email'];
$name = $_SESSION['name'];
?>

<!-- HEADER -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="dash.php?q=0"><b>📘 Teacher Dashboard</b></a>
    </div>
    <ul class="nav navbar-nav">
      <li <?php if(@$_GET['q']==0) echo'class="active"'; ?>><a href="dash.php?q=0">Home</a></li>
      <li <?php if(@$_GET['q']==1) echo'class="active"'; ?>><a href="dash.php?q=1">Scores</a></li>
      <li <?php if(@$_GET['q']==2) echo'class="active"'; ?>><a href="dash.php?q=2">Ranking</a></li>
      <li class="dropdown <?php if(@$_GET['q']==4 || @$_GET['q']==5) echo'active'; ?>">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Quiz <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="dash.php?q=4">➕ Add Quiz</a></li>
          <li><a href="dash.php?q=5">🗑 Remove Quiz</a></li>
          <li role="separator" class="divider"></li>
          <li><a href="generate_ai_questions.php" style="color:#d32f2f;font-weight:bold;">🤖 AI Question Generator</a></li>
        </ul>
      </li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $name; ?></a></li>
      <li><a href="logout.php?q=dash.php"><span class="glyphicon glyphicon-log-out"></span> Sign Out</a></li>
    </ul>
  </div>
</nav>

<div class="container">

<!-- 🏠 HOME -->
<?php 
if(@$_GET['q']==0) {
$result = mysqli_query($con,"SELECT * FROM quiz WHERE created_by='$email' ORDER BY date DESC") or die('Error');
echo '<div class="panel"><h3>Your Created Quizzes</h3><table class="table table-striped">
<tr><th>S.N.</th><th>Title</th><th>Total Qs</th><th>Marks</th><th>+ve</th><th>-ve</th><th>Time</th></tr>';
$c=1;
while($row = mysqli_fetch_array($result)) {
  echo '<tr>
  <td>'.$c++.'</td>
  <td>'.$row['title'].'</td>
  <td>'.$row['total'].'</td>
  <td>'.$row['sahi']*$row['total'].'</td>
  <td>'.$row['sahi'].'</td>
  <td>'.$row['wrong'].'</td>
  <td>'.$row['time'].' min</td>
  </tr>';
}
if ($c==1) echo '<tr><td colspan="7">No quizzes created yet.</td></tr>';
echo '</table></div>';
}
?>

<!-- 📊 SCORES -->
<?php 
if(@$_GET['q']==1) {
$q=mysqli_query($con,"
  SELECT q.title, u.name, u.college, h.score, h.date 
  FROM history h
  JOIN user u ON u.email = h.email
  JOIN quiz q ON q.eid = h.eid
  WHERE q.created_by = '$email'
  ORDER BY h.date DESC
");
echo '<div class="panel"><h3>📊 Student Scores</h3><table class="table table-striped">
<tr><th>S.N.</th><th>Quiz</th><th>Student</th><th>College</th><th>Score</th><th>Date</th></tr>';
$c=1;
while($row=mysqli_fetch_array($q)) {
  echo '<tr><td>'.$c++.'</td>
  <td>'.$row['title'].'</td>
  <td>'.$row['name'].'</td>
  <td>'.$row['college'].'</td>
  <td>'.$row['score'].'</td>
  <td>'.$row['date'].'</td></tr>';
}
if ($c==1) echo '<tr><td colspan="6">No scores yet.</td></tr>';
echo '</table></div>';
}
?>

<!-- 🏆 NEW AJAX RANKING --> <?php if(@$_GET['q']==2) { ?>
<div class="panel">
  <h3>🏆 Leaderboard</h3>

  <div class="form-group">
    <label for="quizSelect">Select Quiz:</label>
    <select id="quizSelect" class="form-control" style="max-width:420px;">
      <option value="">-- Choose a Quiz --</option>
      <?php
      // show only quizzes created by this teacher
      $safe_email = mysqli_real_escape_string($con, $email);
      $quizQuery = mysqli_query($con, "SELECT eid, title FROM quiz WHERE created_by='$safe_email' ORDER BY date DESC");
      while ($quiz = mysqli_fetch_assoc($quizQuery)) {
          echo '<option value="'.htmlspecialchars($quiz['eid']).'">'.htmlspecialchars($quiz['title']).'</option>';
      }
      ?>
    </select>
    <p class="small-note">Choose a quiz to see students who took it (sorted by score). Top 3 get medals.</p>
  </div>

  <div id="rankingTable" style="margin-top:20px;">
    <p>Select a quiz to view rankings.</p>
  </div>
</div>

<script>
(function($){
  function esc(s){ return s===null?'':String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }

  $('#quizSelect').on('change', function(){
    var eid = $(this).val();
    if (!eid) {
      $('#rankingTable').html('<p>Select a quiz to view rankings.</p>');
      return;
    }

    $('#rankingTable').html('<p>Loading rankings...</p>');

    $.getJSON('fetch_ranking.php', { eid: eid })
      .done(function(data){
        if (data.error) {
          $('#rankingTable').html('<p style="color:red;">' + esc(data.error) + '</p>');
          return;
        }

        var html = '<table class="table table-striped"><thead><tr><th>Rank</th><th>Name</th><th>Gender</th><th>College</th><th>Score</th></tr></thead><tbody>';

        if (!data || data.length === 0) {
          html += '<tr><td colspan="5">No results yet for this quiz.</td></tr>';
        } else {
          data.forEach(function(row, i){
            var medal = '';
            if (i === 0) medal = '🥇';
            else if (i === 1) medal = '🥈';
            else if (i === 2) medal = '🥉';

            var rankCell = medal ? '<span class="medal">' + medal + '</span>' : (i+1);
            html += '<tr>' +
                      '<td>' + rankCell + '</td>' +
                      '<td>' + esc(row.name) + '</td>' +
                      '<td>' + esc(row.gender) + '</td>' +
                      '<td>' + esc(row.college) + '</td>' +
                      '<td><b>' + esc(row.score) + '</b></td>' +
                    '</tr>';
          });
        }

        html += '</tbody></table>';
        $('#rankingTable').html(html);
      })
      .fail(function(xhr, status, err){
        $('#rankingTable').html('<p style="color:red;">Failed to load rankings. Open browser console for details.</p>');
        console.error('fetch_ranking failed:', status, err, xhr.responseText);
      });
  });
})(jQuery);
</script>
<?php } ?>


<!-- ➕ ADD QUIZ -->
<?php
if(@$_GET['q']==4 && !isset($_GET['step'])) {
echo '
<div class="panel">
<h3>➕ Create New Quiz</h3>
<form class="form-horizontal" action="update.php?q=addquiz" method="POST">
  <div class="form-group">
    <label class="col-md-3 control-label">Quiz Title:</label>
    <div class="col-md-6">
      <input name="name" class="form-control" placeholder="Enter quiz title" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">Total Questions:</label>
    <div class="col-md-6">
      <input name="total" class="form-control" type="number" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">Marks for Correct:</label>
    <div class="col-md-6">
      <input name="right" class="form-control" type="number" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">Negative Marks:</label>
    <div class="col-md-6">
      <input name="wrong" class="form-control" type="number" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label">Time Limit (minutes):</label>
    <div class="col-md-6">
      <input name="time" class="form-control" type="number" required>
    </div>
  </div>
  <div class="form-group text-center">
    <button type="submit" class="btn btn-purple btn-lg">Create Quiz</button>
  </div>
</form>
</div>';
}
?>

<!-- ✏️ ADD QUESTIONS -->
<?php
if (@$_GET['q']==4 && @$_GET['step']==2) {
    $eid = $_GET['eid'];
    $n   = $_GET['n'];
    echo '<div class="panel"><h3>✏️ Add Questions</h3>
    <form class="form-horizontal" action="update.php?q=addqns&n='.$n.'&eid='.$eid.'" method="POST">';
    for ($i=1; $i<=$n; $i++) {
        echo '
        <fieldset>
        <legend>Question '.$i.'</legend>
        <div class="form-group">
          <label class="col-md-3 control-label">Question Text:</label>
          <div class="col-md-7"><textarea rows="3" name="qns'.$i.'" class="form-control" required></textarea></div>
        </div>
        <div class="form-group"><label class="col-md-3 control-label">Option A:</label>
          <div class="col-md-6"><input name="'.$i.'1" class="form-control" required></div></div>
        <div class="form-group"><label class="col-md-3 control-label">Option B:</label>
          <div class="col-md-6"><input name="'.$i.'2" class="form-control" required></div></div>
        <div class="form-group"><label class="col-md-3 control-label">Option C:</label>
          <div class="col-md-6"><input name="'.$i.'3" class="form-control" required></div></div>
        <div class="form-group"><label class="col-md-3 control-label">Option D:</label>
          <div class="col-md-6"><input name="'.$i.'4" class="form-control" required></div></div>
        <div class="form-group"><label class="col-md-3 control-label">Correct Answer:</label>
          <div class="col-md-6">
            <select name="ans'.$i.'" class="form-control">
              <option value="A">Option A</option>
              <option value="B">Option B</option>
              <option value="C">Option C</option>
              <option value="D">Option D</option>
            </select>
          </div></div>
        </fieldset>';
    }
    echo '<div class="form-group text-center">
      <button type="submit" class="btn btn-purple btn-lg">✅ Save Questions</button>
    </div></form></div>';
}
?>

<!-- 🗑 REMOVE QUIZ -->
<?php 
if(@$_GET['q']==5) {
$result = mysqli_query($con,"SELECT * FROM quiz WHERE created_by='$email' ORDER BY date DESC") or die('Error');
echo '<div class="panel"><h3>🗑 Remove Quiz</h3><table class="table table-striped">
<tr><th>S.N.</th><th>Title</th><th>Total</th><th>Marks</th><th>Time</th><th>Action</th></tr>';
$c=1;
while($row = mysqli_fetch_array($result)) {
  echo '<tr>
  <td>'.$c++.'</td>
  <td>'.$row['title'].'</td>
  <td>'.$row['total'].'</td>
  <td>'.$row['sahi']*$row['total'].'</td>
  <td>'.$row['time'].' min</td>
  <td><a href="update.php?q=rmquiz&eid='.$row['eid'].'" class="btn btn-danger btn-sm">
    <span class="glyphicon glyphicon-trash"></span> Delete</a></td></tr>';
}
if ($c==1) echo '<tr><td colspan="6">No quizzes found.</td></tr>';
echo '</table></div>';
}
?>

</div>
</body>
</html>
