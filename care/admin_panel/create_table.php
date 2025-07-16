<?php
include '../connection.php';

$sql = "CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) UNIQUE NOT NULL,
    content LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($connection->query($sql)) {
    echo "Table 'pages' created successfully or already exists.\n";
} else {
    echo "Error creating table: " . $connection->error . "\n";
}

$connection->close();
?>