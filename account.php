<?php
// account.php - student dashboard & quiz page (fixed question rendering)
// Show errors during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'dbConnection.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("location:index.php");
    exit();
}

$name = $_SESSION['name'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Dashboard | Online Examiner</title>

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
    background: linear-gradient(135deg, #007BFF, #00BCD4);
    border: none;
    border-radius: 0;
}
.navbar-brand, .navbar-nav li a {
    color: white !important;
    font-weight: 500;
}
.navbar-nav li.active a {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #fff !important;
}
.panel {
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border-radius: 10px;
    padding: 25px;
}
.btn-primary {
    background: #007BFF;
    border: none;
    border-radius: 6px;
}
.btn-primary:hover {
    background: #0056b3;
}
h2.title {
    color: #007BFF;
    font-weight: 700;
}
.table thead {
    background: linear-gradient(135deg, #007BFF, #00BCD4);
    color: white;
}
</style>
</head>

<body>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="account.php?q=1"><b>Dashboard - Student</b></a>
    </div>
    <ul class="nav navbar-nav">
      <li <?php if(@$_GET['q']==1) echo'class="active"'; ?>><a href="account.php?q=1">🏠 Home</a></li>
      <li <?php if(@$_GET['q']==2) echo'class="active"'; ?>><a href="account.php?q=2">📜 History</a></li>
      <li <?php if(@$_GET['q']==3) echo'class="active"'; ?>><a href="account.php?q=3">🏆 Ranking</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($name); ?></a></li>
      <li><a href="logout.php?q=account.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<div class="row">
<div class="col-md-12">

<?php
// ----------------------- HOME (list quizzes) -----------------------
if (@$_GET['q'] == 1) {
    $result = mysqli_query($con, "SELECT * FROM quiz ORDER BY date DESC") or die('Error loading quiz list');

    echo '<div class="panel"><h2 class="title">📘 Available Quizzes</h2><table class="table table-striped">
    <thead><tr><th>S.N.</th><th>Title</th><th>Total Qs</th><th>Marks</th><th>+ve</th><th>-ve</th><th>Time (min)</th><th></th></tr></thead><tbody>';

    $c = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $eid = $row['eid'];
        $title = $row['title'];
        $total = $row['total'];
        $sahi = $row['sahi'];
        $wrong = $row['wrong'];
        $time = $row['time'];

        $q12 = mysqli_query($con, "SELECT score FROM history WHERE eid='$eid' AND email='$email'");
        $rowcount = mysqli_num_rows($q12);

        if ($rowcount == 0) {
            echo '<tr><td>'. $c++ .'</td><td>'.htmlspecialchars($title).'</td><td>'.$total.'</td><td>'.($sahi*$total).'</td>
            <td>'.$sahi.'</td><td>'.$wrong.'</td><td>'.$time.'</td>
            <td><a href="account.php?q=quiz&step=2&eid='.$eid.'&n=1&t='.$total.'" class="btn btn-primary">Start Quiz</a></td></tr>';
        } else {
            echo '<tr style="color:#28a745"><td>'. $c++ .'</td><td>'.htmlspecialchars($title).' ✅</td><td>'.$total.'</td>
            <td>'.($sahi*$total).'</td><td>'.$sahi.'</td><td>'.$wrong.'</td><td>'.$time.'</td><td>Completed</td></tr>';
        }
    }
    echo '</tbody></table></div>';
}

// ----------------------- SHOW QUESTION (OLD + NEW QUIZ SUPPORT) -----------------------
if (@$_GET['q'] == 'quiz' && @$_GET['step'] == 2) {
    $eid = mysqli_real_escape_string($con, $_GET['eid'] ?? '');
    $sn  = intval($_GET['n'] ?? 0);
    $total = intval($_GET['t'] ?? 0);

    if ($eid === '' || $sn <= 0) {
        echo '<div class="panel"><h4>⚠️ Invalid quiz or question number.</h4></div>';
    } else {
        // 🔹 Try new format first
        $q = mysqli_query($con, "SELECT * FROM questions WHERE eid='$eid' AND qn='$sn' LIMIT 1");
        // 🔹 If not found, fallback to old structure (use sn column)
        if (mysqli_num_rows($q) == 0) {
            $q = mysqli_query($con, "SELECT * FROM questions WHERE eid='$eid' AND sn='$sn' LIMIT 1");
        }

        if (!$q || mysqli_num_rows($q) == 0) {
            echo '<div class="panel"><h4>⚠️ Question not found.</h4></div>';
        } else {
            $row = mysqli_fetch_assoc($q);
            $qid = $row['qid'] ?? uniqid();
            $questionText = $row['qns'] ?? $row['question'] ?? '';
            $optiona = $row['optiona'] ?? '';
            $optionb = $row['optionb'] ?? '';
            $optionc = $row['optionc'] ?? '';
            $optiond = $row['optiond'] ?? '';

            echo '<div class="panel">';
            echo '<h4><b>Question '.$sn.':</b> '.htmlspecialchars($questionText).'</h4>';
            echo '<form action="update.php?q=quiz&step=2&eid='.urlencode($eid).'&n='.urlencode($sn).'&t='.urlencode($total).'&qid='.urlencode($qid).'" method="POST">';
            echo '<div class="radio"><label><input type="radio" name="ans" value="A" required> '.htmlspecialchars($optiona).'</label></div>';
            echo '<div class="radio"><label><input type="radio" name="ans" value="B" required> '.htmlspecialchars($optionb).'</label></div>';
            echo '<div class="radio"><label><input type="radio" name="ans" value="C" required> '.htmlspecialchars($optionc).'</label></div>';
            echo '<div class="radio"><label><input type="radio" name="ans" value="D" required> '.htmlspecialchars($optiond).'</label></div>';
            echo '<br><button type="submit" class="btn btn-success">Submit Answer</button></form>';
            echo '</div>';
        }
    }
}

// ----------------------- HISTORY (q=2) -----------------------
if (@$_GET['q'] == 2) {
    $result = mysqli_query($con, "SELECT * FROM history WHERE email='$email' ORDER BY date DESC") or die('Error loading history');
    echo '<div class="panel"><h2 class="title">📜 Your Quiz History</h2>';

    if (mysqli_num_rows($result) == 0) {
        echo '<p>No quiz history yet.</p>';
    } else {
        echo '<table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Quiz Title</th>
                        <th>Score</th>
                        <th>Correct</th>
                        <th>Wrong</th>
                        <th>Attempted</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>';
        $c = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $eid   = $row['eid'];
            $score = (int)$row['score'];
            $sahi  = (int)$row['sahi'];
            $wrong = (int)$row['wrong'];
            $level = (int)$row['level']; // questions attempted
            $date  = $row['date'];

            // fetch quiz title
            $qz = mysqli_query($con, "SELECT title FROM quiz WHERE eid='$eid' LIMIT 1");
            $title = ($qz && mysqli_num_rows($qz)) ? mysqli_fetch_assoc($qz)['title'] : '—';

            echo '<tr>
                    <td>'.($c++).'</td>
                    <td>'.htmlspecialchars($title).'</td>
                    <td>'.$score.'</td>
                    <td>'.$sahi.'</td>
                    <td>'.$wrong.'</td>
                    <td>'.$level.'</td>
                    <td>'.$date.'</td>
                  </tr>';
        }
        echo '  </tbody>
              </table>';
    }
    echo '</div>';
}

// ----------------------- RANKING (q=3) -----------------------
if (@$_GET['q'] == 3) {
    $result = mysqli_query($con, "SELECT * FROM rank ORDER BY score DESC, time ASC") or die('Error loading ranking');
    echo '<div class="panel"><h2 class="title">🏆 Global Ranking</h2>';

    if (mysqli_num_rows($result) == 0) {
        echo '<p>No rankings yet.</p>';
    } else {
        echo '<table class="table table-striped">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>College</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>';
        $r = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $remail = $row['email'];
            $score  = (int)$row['score'];
            $u = mysqli_query($con, "SELECT name, college FROM user WHERE email='$remail' LIMIT 1");
            $uname = '—'; $ucollege = '—';
            if ($u && mysqli_num_rows($u)) {
                $ud = mysqli_fetch_assoc($u);
                $uname = $ud['name'];
                $ucollege = $ud['college'];
            }
            echo '<tr>
                    <td>'.$r++.'</td>
                    <td>'.htmlspecialchars($uname).'</td>
                    <td>'.htmlspecialchars($ucollege).'</td>
                    <td>'.$score.'</td>
                  </tr>';
        }
        echo '  </tbody>
              </table>';
    }
    echo '</div>';
}


// ----------------------- RESULT PAGE (after quiz completion) -----------------------
if (@$_GET['q'] == 'result' && @$_GET['eid']) {
    $eid = mysqli_real_escape_string($con, $_GET['eid']);
    $q = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND email='$email'");

    echo '<div class="panel"><h2 class="title">🎯 Quiz Result</h2>';
    if (mysqli_num_rows($q) == 0) {
        echo '<p style="color:red;">⚠️ No result found for this quiz.</p>';
    } else {
        echo '<table class="table table-bordered">';
        while ($row = mysqli_fetch_assoc($q)) {
            echo '<tr><td><b>Total Questions</b></td><td>'.htmlspecialchars($row['level']).'</td></tr>
                  <tr><td><b>Correct Answers</b></td><td>'.htmlspecialchars($row['sahi']).'</td></tr>
                  <tr><td><b>Wrong Answers</b></td><td>'.htmlspecialchars($row['wrong']).'</td></tr>
                  <tr><td><b>Score</b></td><td>'.htmlspecialchars($row['score']).'</td></tr>';
        }

        $r = mysqli_query($con, "SELECT score FROM rank WHERE email='$email'");
        if ($r && mysqli_num_rows($r) > 0) {
            $rank_row = mysqli_fetch_assoc($r);
            echo '<tr><td><b>Overall Rank Score</b></td><td>'.htmlspecialchars($rank_row['score']).'</td></tr>';
        }

        echo '</table>';
    }
    echo '<a href="account.php?q=1" class="btn btn-primary">Return to Dashboard</a></div>';
}
?>
