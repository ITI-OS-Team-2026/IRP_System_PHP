<?php

require __DIR__ . '/../config/database.php';

// --- Load .env variables ---
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            $parts = explode('=', $line, 2);
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            if (
                strlen($value) >= 2 &&
                (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))
            ) {
                $value = substr($value, 1, -1);
            }
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

$conn = Database::getConnection();

echo "<body style='font-family: sans-serif; line-height: 1.6; padding: 20px;'>";
echo "<h1>🚀 Advanced Database Migration</h1>";

// 1. Disable Foreign Keys
$conn->query("SET FOREIGN_KEY_CHECKS = 0;");

// 2. Drop all known tables
$tables = ['system_logs', 'reviews', 'payments', 'research_documents', 'research_submissions', 'students', 'users'];
foreach ($tables as $table) {
    if ($conn->query("DROP TABLE IF EXISTS $table")) {
        echo "<li style='color: orange;'>Dropped table: <b>$table</b></li>";
    }
}

// 3. Re-enable Foreign Keys
$conn->query("SET FOREIGN_KEY_CHECKS = 1;");

echo "<hr>";

// 4. Load and Split Schema
$sqlFile = __DIR__ . '/schema.sql';
if (!file_exists($sqlFile)) {
    die("<p style='color: red;'>❌ Schema file not found!</p>");
}

$sql = file_get_contents($sqlFile);

// Split by semicolon, but avoid splitting inside triggers or strings if any
// For this simple schema, splitting by ');' or ';' is usually enough
$queries = explode(';', $sql);

$successCount = 0;
foreach ($queries as $query) {
    $query = trim($query);
    if (empty($query)) continue;

    if ($conn->query($query)) {
        $successCount++;
        // Try to find table name in query for better logging
        if (preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/i', $query, $matches)) {
            echo "<li style='color: green;'>Created table: <b>{$matches[1]}</b></li>";
        }
    } else {
        echo "<li style='color: red;'>❌ Error in query: " . $conn->error . "<br><pre style='background: #fee; padding: 10px;'>" . htmlspecialchars($query) . "</pre></li>";
    }
}

echo "<hr>";
echo "<h3>✅ Migration Finished. $successCount queries executed.</h3>";
echo "<p><a href='../public/register' style='background: #312E81; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Registration</a></p>";
echo "</body>";
