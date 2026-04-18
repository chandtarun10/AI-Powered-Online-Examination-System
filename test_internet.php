<?php
$response = @file_get_contents("https://www.google.com");
if ($response === FALSE) {
    echo "🚫 No internet access from PHP.";
} else {
    echo "✅ PHP can access the internet!";
}
?>
