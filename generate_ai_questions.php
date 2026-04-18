<?php
session_start();
if(!isset($_SESSION['email'])) {
    header("location:index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>AI Question Generator</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      background: linear-gradient(135deg,#f4511e,#ff9800);
      font-family: 'Poppins', sans-serif;
      color: #fff;
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }
    .container {
      margin-top: 80px;
      background: rgba(255,255,255,0.12);
      border-radius: 20px;
      padding: 30px;
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      max-width: 700px;
    }
    h2 { text-align:center; margin-bottom:25px; font-weight:700; color:#fff; }
    label { font-weight: 500; color: #fff; }
    input, textarea {
      background-color: rgba(255,255,255,0.95);
      border: none;
      border-radius: 10px;
      color:#333;
    }
    .btn-ai {
      background: #fff;
      color:#f4511e;
      font-weight:600;
      border:none;
      border-radius:12px;
      padding:10px 25px;
      transition:0.3s;
    }
    .btn-ai:hover {
      background:#f4511e;
      color:#fff;
      transform:scale(1.05);
    }
    .output {
      white-space: pre-wrap;
      margin-top:25px;
      background:#fff;
      color:#333;
      padding:20px;
      border-radius:12px;
      display:none;
      font-size: 15px;
    }
    .loading {
      text-align:center;
      font-weight:bold;
      color:#fff;
      animation: blink 1s infinite;
    }
    @keyframes blink {
      50% { opacity: 0.5; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🤖 AI Question Generator</h2>
    <p style="text-align:center;">Enter a topic and let AI generate and save sample MCQs automatically!</p>
    
    <form id="aiForm">
      <div class="form-group">
        <label>Topic Name:</label>
        <input type="text" name="topic" class="form-control" required placeholder="e.g., Database Management System">
      </div>
      <div class="form-group">
        <label>Number of Questions:</label>
        <input type="number" name="count" class="form-control" value="5" min="1" max="10">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-ai btn-block">Generate & Save Questions</button>
      </div>
    </form>

    <div id="loading" class="loading" style="display:none;">⏳ Generating questions... please wait...</div>
    <div id="result" class="output"></div>
  </div>

<script>
$("#aiForm").submit(function(e){
    e.preventDefault();
    $("#result").hide();
    $("#loading").show();

    const topic = $("input[name='topic']").val();
    const count = $("input[name='count']").val();

    $.ajax({
        url: "process_ai_save.php",
        type: "POST",
        data: { topic: topic, count: count },
        success: function(response){
            $("#loading").hide();
            $("#result").show().html(response);
        },
        error: function(){
            $("#loading").hide();
            $("#result").show().html("❌ Error: Unable to generate questions.");
        }
    });
});
</script>
</body>
</html>
