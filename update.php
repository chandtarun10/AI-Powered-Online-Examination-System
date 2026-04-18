<?php
// ✅ Unified update.php for Online Examiner
// Handles teacher + admin actions + student quiz submission

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'dbConnection.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$session_email = $_SESSION['email'];
$session_key   = $_SESSION['key'] ?? null;

// Helper function to stop and show a message
function abort_msg($msg) {
    echo "<div style='padding:20px;background:#fee;border:1px solid #f99;color:#900;'>Error: " . htmlspecialchars($msg) . "</div>";
    exit();
}

// ====================================================================
// 🗑 DELETE FEEDBACK (Admin only)
// ====================================================================
if (isset($_GET['fdid'])) {
    if ($session_key === 'tarun123') {
        $id = mysqli_real_escape_string($con, $_GET['fdid']);
        mysqli_query($con, "DELETE FROM feedback WHERE id='$id'");
        header("Location: headdash.php?q=3");
        exit();
    } else abort_msg("Only admin can delete feedback.");
}

// ====================================================================
// 🗑 DELETE USER (Admin only)
// ====================================================================
if (isset($_GET['demail'])) {
    if ($session_key === 'tarun123') {
        $demail = mysqli_real_escape_string($con, $_GET['demail']);
        mysqli_query($con, "DELETE FROM rank WHERE email='$demail'");
        mysqli_query($con, "DELETE FROM history WHERE email='$demail'");
        mysqli_query($con, "DELETE FROM user WHERE email='$demail'");
        header("Location: headdash.php?q=1");
        exit();
    } else abort_msg("Only admin can delete users.");
}

// ====================================================================
// 🗑 DELETE ADMIN (Admin only, with quiz deletion option)
// ====================================================================
if (isset($_GET['demail1'])) {
    if ($session_key === 'tarun123') {
        $demail1 = mysqli_real_escape_string($con, $_GET['demail1']);
        $remove_quizzes = $_GET['remove_quizzes'] ?? 'no';

        // default message: only teacher removed
        $msg = 'teacher_removed';

        // ✅ Step 1: Remove all quizzes (if confirmed)
        if ($remove_quizzes === 'yes') {
            $quiz_result = mysqli_query($con, "SELECT eid FROM quiz WHERE created_by='$demail1'");
            while ($quiz = mysqli_fetch_assoc($quiz_result)) {
                $eid = $quiz['eid'];

                // delete all related records
                mysqli_query($con, "DELETE FROM questions WHERE eid='$eid'");
                mysqli_query($con, "DELETE FROM history WHERE eid='$eid'");
                mysqli_query($con, "DELETE FROM quiz WHERE eid='$eid'");
            }

            // if quizzes were removed too, change message
            $msg = 'teacher_removed_quizzes';
        }

        // ✅ Step 2: Delete the teacher record itself
        mysqli_query($con, "DELETE FROM admin WHERE email='$demail1' AND role='admin'");

        // ✅ Step 3: Redirect back safely with proper msg flag
        header("Location: headdash.php?q=5&msg=" . $msg);
        exit();
    } else {
        abort_msg("Only admin can delete teachers.");
    }
}



// ====================================================================
// 🗑 REMOVE QUIZ (Admin or Quiz Creator)
// ====================================================================
if (isset($_GET['q']) && $_GET['q'] === 'rmquiz' && isset($_GET['eid'])) {
    $eid = mysqli_real_escape_string($con, $_GET['eid']);

    // Check if current user is creator or head/teacher (admin)
    $quizQ = mysqli_query($con, "SELECT email FROM quiz WHERE eid='$eid' LIMIT 1");
    if (!$quizQ || mysqli_num_rows($quizQ) == 0) {
        abort_msg("Quiz not found.");
    }
    $quiz = mysqli_fetch_assoc($quizQ);
    $creator_email = $quiz['email'];

    if ($session_key !== 'tarun123' && $session_email !== $creator_email) {
        abort_msg("You are not allowed to delete this quiz.");
    }

    // Delete related data
    mysqli_query($con, "DELETE FROM questions WHERE eid='$eid'");
    mysqli_query($con, "DELETE FROM history   WHERE eid='$eid'");
    mysqli_query($con, "DELETE FROM quiz      WHERE eid='$eid'");

    if ($session_email === $creator_email && $session_key !== 'tarun123') {
        header("Location: dash.php?q=0");
    } else {
        header("Location: headdash.php?q=0");
    }
    exit();
}

// ====================================================================
// 🧠 FIXED: QUIZ ANSWER SUBMISSION (Student)
// ====================================================================
if (isset($_GET['q']) && $_GET['q'] === 'quiz' && isset($_GET['step']) && $_GET['step'] == 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $eid = mysqli_real_escape_string($con, $_GET['eid']);
    $sn  = intval($_GET['n']);
    $total = intval($_GET['t']);
    $qid = mysqli_real_escape_string($con, $_GET['qid']);
    $ans = mysqli_real_escape_string($con, $_POST['ans'] ?? '');
    $email = $_SESSION['email'];

    // ✅ fetch correct answer
    // ✅ fetch correct answer (support old quizzes without qid match)
    $query = mysqli_query($con, "SELECT ans FROM questions WHERE qid='$qid' LIMIT 1");

    // fallback for old quizzes where qid may not match but qn/sn exist
    if (!$query || mysqli_num_rows($query) == 0) {
        $sn_safe = intval($_GET['n']);
        $eid_safe = mysqli_real_escape_string($con, $_GET['eid']);
        $query = mysqli_query($con, "SELECT ans FROM questions WHERE eid='$eid_safe' AND (qn='$sn_safe' OR sn='$sn_safe') LIMIT 1");
    }

    if (!$query || mysqli_num_rows($query) == 0) {
        abort_msg("Question not found for submission.");
    }

    $correct_row = mysqli_fetch_assoc($query);

    $correct = strtoupper(trim($correct_row['ans']));
    $is_correct = (strtoupper($ans) === $correct);

    // ✅ get quiz scoring rules
    $quiz_q = mysqli_query($con, "SELECT sahi, wrong, total FROM quiz WHERE eid='$eid' LIMIT 1");
    $quiz = mysqli_fetch_assoc($quiz_q);
    $sahi = intval($quiz['sahi']);
    $wrong = intval($quiz['wrong']);
    $totalq = intval($quiz['total']);

    // ✅ ensure history row exists
    $checkHist = mysqli_query($con, "SELECT * FROM history WHERE eid='$eid' AND email='$email'");
    if (mysqli_num_rows($checkHist) == 0) {
        mysqli_query($con, "INSERT INTO history (email, eid, score, level, sahi, wrong, date)
                            VALUES ('$email', '$eid', 0, 0, 0, 0, NOW())");
    }

    // ✅ update history based on correctness
    if ($is_correct) {
        mysqli_query($con, "UPDATE history SET score = score + $sahi, level = level + 1, sahi = sahi + 1, date = NOW()
                            WHERE email='$email' AND eid='$eid'");
    } else {
        mysqli_query($con, "UPDATE history SET score = score - $wrong, level = level + 1, wrong = wrong + 1, date = NOW()
                            WHERE email='$email' AND eid='$eid'");
    }

    // ✅ next question or finish
    if ($sn < $total) {
        $sn++;
        header("Location: account.php?q=quiz&step=2&eid=$eid&n=$sn&t=$total");
    } else {
        $scoreQuery = mysqli_query($con, "SELECT score FROM history WHERE eid='$eid' AND email='$email'");
        $row = mysqli_fetch_assoc($scoreQuery);
        $finalScore = intval($row['score']);

        $rankQ = mysqli_query($con, "SELECT * FROM rank WHERE email='$email'");
        if (mysqli_num_rows($rankQ) == 0) {
            mysqli_query($con, "INSERT INTO rank (email, score, time) VALUES ('$email', $finalScore, NOW())");
        } else {
            mysqli_query($con, "UPDATE rank SET score = score + $finalScore, time = NOW() WHERE email='$email'");
        }

        if (!headers_sent()) {
            header("Location: account.php?q=result&eid=$eid");
            exit();
        } else {
            echo "<div style='padding:20px;border:1px solid #ccc;background:#fff3cd;color:#856404;'>
                ⚠️ Redirect failed (headers already sent).<br>
                <b>Try manually visiting:</b> 
                <a href='account.php?q=result&eid=$eid'>account.php?q=result&eid=$eid</a>
            </div>";
            exit();
        }

    }
    exit();
}

// ====================================================================
// ❌ Default (no action matched)
// ====================================================================
abort_msg("No valid action requested.");
?>
