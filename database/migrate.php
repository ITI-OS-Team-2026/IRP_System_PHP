<?php

require __DIR__ . '/../public/index.php';
require __DIR__ . '/../config/database.php';

$conn = Database::getConnection();

echo "<h2>Cleaning and Rebuilding Database...</h2>";

$tables = ['system_logs', 'reviews', 'payments', 'research_documents', 'research_submissions', 'students', 'users'];
$conn->query("SET FOREIGN_KEY_CHECKS = 0;");
foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS $table;");
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1;");

echo "<p style='color: orange;'>⚠️ Old tables removed.</p>";


$sqlFile = __DIR__ . '/schema.sql';

if (!file_exists($sqlFile)) {
    die("Schema file not found at $sqlFile");
}

$sql = file_get_contents($sqlFile);

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "<p style='color: green;'>✅ Database rebuilt successfully with the latest structure!</p>";
    echo "<p>You can now test the registration.</p>";
} else {
    echo "<p style='color: red;'>❌ Error building schema: " . $conn->error . "</p>";
}
