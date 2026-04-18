<?php
ob_start(); // Prevent header issues
session_start();
include_once 'dbConnection.php';

// Ensure the request came via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $topic = trim($_POST['topic']);
    $count = intval($_POST['count']);

    // 🔒 Replace this with your OWN OpenAI API key (keep it private!)
    $apiKey = "your_api_key"; // <-- your full key here

    // --- Safety check ---
    if ($apiKey === "YOUR_OPENAI_API_KEY_HERE" || empty($apiKey)) {
        echo "⚠️ Please add your OpenAI API key in process_ai_save.php.";
        exit();
    }

    // --- AI prompt ---
    $prompt = "Generate $count multiple choice questions on $topic in valid JSON format. 
    Each question should include:
    {
      'question': '...',
      'options': {'A':'...', 'B':'...', 'C':'...', 'D':'...'},
      'answer': 'A/B/C/D'
    }";

    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "You are an AI that generates structured JSON exam questions only."],
            ["role" => "user", "content" => $prompt]
        ]
    ];

    // --- Set up API request ---
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "❌ cURL Error: " . curl_error($ch);
    curl_close($ch);
    exit();
}
curl_close($ch);


    // --- Error handling for API ---
    if ($response === FALSE) {
        echo "❌ Error: Unable to connect to OpenAI API. Check your API key or internet.";
        exit();
    }

    $result = json_decode($response, true);

// ✅ Debug log to inspect raw API response
file_put_contents("ai_debug_log.txt", date("Y-m-d H:i:s") . "\n" . print_r($result, true) . "\n\n", FILE_APPEND);

if (!isset($result['choices'][0])) {
    echo "❌ Error: Unexpected API response. Full response logged in ai_debug_log.txt";
    exit();
}

// Handle both possible response formats
if (isset($result['choices'][0]['message']['content'])) {
    $aiText = $result['choices'][0]['message']['content'];
} elseif (isset($result['choices'][0]['text'])) {
    $aiText = $result['choices'][0]['text'];
} else {
    echo "⚠️ API did not return expected text output. Check ai_debug_log.txt for details.";
    exit();
}


    // 🧹 Clean JSON (remove extra text if AI adds explanation)
    $jsonStart = strpos($aiText, '{');
    $jsonEnd = strrpos($aiText, '}');
    if ($jsonStart !== false && $jsonEnd !== false) {
        $aiText = substr($aiText, $jsonStart, $jsonEnd - $jsonStart + 1);
    }

    // Try to decode JSON output
    $questions = json_decode($aiText, true);

    if (!$questions || !is_array($questions)) {
        echo "⚠️ Error: AI response was not valid JSON. Please try again.";
        file_put_contents("ai_error_log.txt", date("Y-m-d H:i:s") . "\n$aiText\n\n", FILE_APPEND);
        exit();
    }

    // --- Save Quiz Info ---
    $eid = uniqid();
    $date = date("Y-m-d");
    $creator = $_SESSION['email'];

    $insertQuiz = "INSERT INTO quiz (eid, title, total, sahi, wrong, time, date, created_by) 
                   VALUES ('$eid', '$topic', '$count', 1, 0, 10, '$date', '$creator')";
    mysqli_query($con, $insertQuiz) or die("❌ Error saving quiz: " . mysqli_error($con));

    // --- Save Each Question ---
    $qn = 1;
    foreach ($questions as $q) {
        $qid = uniqid();
        $question = mysqli_real_escape_string($con, $q['question']);
        $a = mysqli_real_escape_string($con, $q['options']['A']);
        $b = mysqli_real_escape_string($con, $q['options']['B']);
        $c = mysqli_real_escape_string($con, $q['options']['C']);
        $d = mysqli_real_escape_string($con, $q['options']['D']);
        $ans = mysqli_real_escape_string($con, $q['answer']);

        $insertQ = "INSERT INTO questions (eid, qid, qn, question, optiona, optionb, optionc, optiond, ans) 
                    VALUES ('$eid', '$qid', '$qn', '$question', '$a', '$b', '$c', '$d', '$ans')";
        mysqli_query($con, $insertQ) or die("❌ Error saving question: " . mysqli_error($con));
        $qn++;
    }

    // --- Optional logging ---
    file_put_contents("ai_logs.txt", date("Y-m-d H:i:s") . "\n$aiText\n\n", FILE_APPEND);

    // ✅ Final Output
    echo "<div style='padding:15px; background:#e8f5e9; color:#2e7d32; border-radius:10px; font-size:18px;'>
            ✅ <b>$count</b> questions on <b>$topic</b> successfully generated and saved! 🎉
          </div>
          <hr>
          <pre style='background:#fff; color:#333; padding:15px; border-radius:10px;'>
          ".htmlspecialchars(json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))."
          </pre>";
}

ob_end_flush();
?>
