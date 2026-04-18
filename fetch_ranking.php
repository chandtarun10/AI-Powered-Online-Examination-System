<?php
// fetch_ranking.php
header('Content-Type: application/json; charset=utf-8');
include_once 'dbConnection.php';

if (!isset($_GET['eid']) || trim($_GET['eid']) === '') {
    echo json_encode(['error' => 'quiz id (eid) is required']);
    exit;
}

$eid = $_GET['eid'];

// protect against injection (we use mysqli_real_escape_string); if you use prepared statements, even better
$eid_safe = mysqli_real_escape_string($con, $eid);

// Pull history rows for this quiz, join user info, order by score desc, then by date asc
$sql = "
    SELECT u.name, u.gender, u.college, h.score
    FROM history h
    JOIN user u ON h.email = u.email
    WHERE h.eid = '$eid_safe'
    ORDER BY h.score DESC, h.date ASC
";
$res = mysqli_query($con, $sql);
if (!$res) {
    echo json_encode(['error' => 'DB error: ' . mysqli_error($con)]);
    exit;
}

$data = [];
$rank = 1;
while ($row = mysqli_fetch_assoc($res)) {
    // enforce simple types
    $data[] = [
        'rank'   => $rank++,
        'name'   => $row['name'] ?? '',
        'gender' => $row['gender'] ?? '',
        'college'=> $row['college'] ?? '',
        'score'  => (int)($row['score'] ?? 0),
    ];
}

echo json_encode($data);
exit;
