<?php
// Simple download handler without external dependencies
// Validate parameters
if (!isset($_GET['file']) || !isset($_GET['type'])) {
    header("HTTP/1.0 400 Bad Request");
    die("Missing parameters");
}

$filename = $_GET['file'];
$type = $_GET['type'];

// Validate file type
if (!in_array($type, ['csv', 'excel'])) {
    header("HTTP/1.0 400 Bad Request");
    die("Invalid file type");
}

// Sanitize filename to prevent directory traversal
$filename = basename($filename);
$file_path = 'uploads/' . $filename;

// Check if file exists
if (!file_exists($file_path)) {
    header("HTTP/1.0 404 Not Found");
    die("File not found");
}

if ($type === 'csv') {
    // Download as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($file_path));
    
    readfile($file_path);
}

// Optional: Clean up the file after download (uncomment if you want to delete after download)
// unlink($file_path);
?>