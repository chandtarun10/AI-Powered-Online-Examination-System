<?php
if (extension_loaded('curl') && extension_loaded('openssl')) {
    echo "✅ Both cURL and OpenSSL are enabled!";
} else {
    echo "❌ Missing: ";
    echo !extension_loaded('curl') ? "cURL " : "";
    echo !extension_loaded('openssl') ? "OpenSSL" : "";
}
?>