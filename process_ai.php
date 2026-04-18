<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $topic = urlencode($_POST['topic']);
    $count = intval($_POST['count']);

    // ✅ Your SerpAPI key
    $apiKey = "your_api_key";

    // Build the SerpAPI request URL
    $url = "https://serpapi.com/search.json?q=$topic+multiple+choice+questions&num=$count&api_key=$apiKey";

    // Fetch results
    $response = @file_get_contents($url);

    if ($response === FALSE) {
        echo "<h4 style='color:red;'>❌ Error contacting SerpAPI service.</h4>";
        exit();
    }

    $data = json_decode($response, true);

    if (isset($data['error'])) {
        echo "<h4 style='color:red;'>⚠️ SerpAPI Error: " . htmlspecialchars($data['error']) . "</h4>";
        exit();
    }

    echo "<div style='font-family:Arial; background:#f8f9fa; padding:20px; border-radius:10px;'>";
    echo "<h3>🧠 AI-Suggested Questions for Topic: " . htmlspecialchars(urldecode($topic)) . "</h3>";

    // Check if organic results exist
    if (!empty($data['organic_results'])) {
        $i = 1;
        foreach ($data['organic_results'] as $result) {
            $title = htmlspecialchars($result['title'] ?? '');
            $snippet = htmlspecialchars($result['snippet'] ?? '');
            $link = htmlspecialchars($result['link'] ?? '#');

            echo "<div style='margin-bottom:15px;'>";
            echo "<b>Q$i.</b> $title<br>";
            echo "<small>$snippet</small><br>";
            echo "<a href='$link' target='_blank'>View Source 🔗</a>";
            echo "</div>";
            $i++;

            if ($i > $count) break;
        }
    } else {
        echo "<p>No results found for '$topic'. Try another topic.</p>";
    }

    echo "</div>";
}
?>
