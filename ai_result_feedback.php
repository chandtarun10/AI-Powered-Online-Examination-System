<?php
session_start();
if(!isset($_SESSION['email'])){
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>AI Result Insights</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style>
    body{
      background: linear-gradient(135deg,#4facfe,#00f2fe);
      color:#fff;
      font-family:'Lato',sans-serif;
      min-height:100vh;
    }
    .container{
      margin-top:60px;
      background:rgba(255,255,255,0.15);
      border-radius:20px;
      padding:30px;
      backdrop-filter:blur(10px);
    }
    .btn-ai{
      background:#fff;
      color:#007acc;
      border:none;
      border-radius:10px;
      font-weight:600;
      padding:10px 25px;
      transition:0.3s;
    }
    .btn-ai:hover{
      background:#007acc;
      color:#fff;
      transform:scale(1.05);
    }
    .output{
      background:#fff;
      color:#333;
      padding:20px;
      border-radius:10px;
      margin-top:20px;
      display:none;
    }
  </style>
</head>
<body>
<div class="container">
  <h2 style="text-align:center;">📈 AI Result Insights</h2>
  <p style="text-align:center;">Get personalized feedback based on your exam results!</p>
  <form method="post">
    <div class="form-group">
      <label>Your Subject:</label>
      <input type="text" name="subject" class="form-control" placeholder="e.g., Operating System" required>
    </div>
    <div class="form-group">
      <label>Your Score:</label>
      <input type="number" name="score" class="form-control" placeholder="e.g., 72" required>
    </div>
    <div class="form-group" style="text-align:center;">
      <button type="submit" name="analyze" class="btn-ai">Get AI Insights</button>
    </div>
  </form>
  <div id="result" class="output"></div>
</div>

<?php
if(isset($_POST['analyze'])){
    $subject = $_POST['subject'];
    $score = $_POST['score'];

    echo "<script>
      document.getElementById('result').style.display='block';
      document.getElementById('result').innerHTML = '⏳ Generating feedback for $subject (Score: $score)...';
    </script>";

    // ---- AI integration (later) ----
    
    $apiKey = "your_api_key";

    $data = [
      "model" => "gpt-3.5-turbo",
      "messages" => [
        ["role" => "system", "content" => "You are a helpful academic advisor."],
        ["role" => "user", "content" => "Give feedback for a student who scored $score% in $subject, suggest areas to improve and resources."]
      ]
    ];
    $options = [
      'http' => [
        'header'  => "Content-type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
      ],
    ];
    $context  = stream_context_create($options);
    $response = file_get_contents('https://api.openai.com/v1/chat/completions', false, $context);
    $result = json_decode($response, true);
    $aiText = $result['choices'][0]['message']['content'];
    echo "<script>document.getElementById('result').innerHTML = `$aiText`;</script>";
    
}
?>
</body>
</html>