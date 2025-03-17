<?php

// Get the filename from the URL parameter
$filename = urldecode($_GET['file'] ?? '');

// Validate filename to prevent unauthorized access (optional)
// You can implement checks based on allowed extensions or directory restrictions

// Check if file exists
if (!file_exists($filename)) {
  die("File not found.");
}

// Open the file for reading
$handle = fopen($filename, "r");
if (!$handle) {
  die("Error opening file.");
}

// Read the entire file content
$content = fread($handle, filesize($filename));

// Close the file
fclose($handle);

// Display the content within an HTML element
echo "<h1>" . basename($filename) . "</h1>";
echo "<pre>" . htmlspecialchars($content) . "</pre>"; // Escape special characters for security

?>
