<?php
$ch = curl_init("https://api.openai.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "❌ cURL error: " . curl_error($ch);
} else {
    echo "✅ Successfully connected to OpenAI API endpoint!";
}

curl_close($ch);
?>
