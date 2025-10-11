<?php
echo "🔍 Laravel Environment Check<br>";
echo "=========================<br>";

// Check if .env exists
if (file_exists(".env")) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file missing<br>";
}

// Check if storage is writable
if (is_writable("storage")) {
    echo "✅ Storage directory is writable<br>";
} else {
    echo "❌ Storage directory is not writable<br>";
}

// Check if bootstrap/cache is writable
if (is_writable("bootstrap/cache")) {
    echo "✅ Bootstrap cache is writable<br>";
} else {
    echo "❌ Bootstrap cache is not writable<br>";
}

// Check database
if (file_exists("database/database.sqlite")) {
    echo "✅ Database file exists<br>";
} else {
    echo "❌ Database file missing<br>";
}

echo "<br>🚀 If all checks pass, your Laravel app should work!<br>";
?>