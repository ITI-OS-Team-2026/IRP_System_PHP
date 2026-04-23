<?php
/**
 * Simple Migration Script
 */

require_once __DIR__ . '/public/index.php'; // Load .env
require_once __DIR__ . '/config/database.php';

$conn = Database::getConnection();

$sqlFile = __DIR__ . '/database/schema.sql';

if (!file_exists($sqlFile)) {
    die("Schema file not found at $sqlFile");
}

$sql = file_get_contents($sqlFile);

echo "<h2>Building Database Schema...</h2>";

// Use multi_query for multiple statements
if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
        // Check if more results are available
    } while ($conn->more_results() && $conn->next_result());
    
    echo "<p style='color: green;'>✅ Schema built successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Error building schema: " . $conn->error . "</p>";
}
