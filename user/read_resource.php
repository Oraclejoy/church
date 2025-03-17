<?php
// Database connection
include('../config.php'); // Make sure you have your config.php file with database connection details

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT file_name, file_type, file_content FROM resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($fileName, $fileType, $fileContent);
    $stmt->fetch();
    $stmt->close();

    if ($fileName) {
        header("Content-Type: $fileType");
        echo $fileContent;
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "No resource ID provided.";
}
?>
