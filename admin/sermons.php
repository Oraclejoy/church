<?php
include '../config.php';


$uploads_dir = 'C:/xampp/htdocs/CHURCH/admin/uploads/'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? null;
    $date = $_POST['date'] ?? null;
    $pastor = $_POST['pastor'] ?? null;
    $description = $_POST['description'] ?? null;
    $youtube_url = $_POST['youtube_url'] ?? null;
    $file_path = null;
    $file_type = null;

    
    if (!empty($_FILES['file']['name'])) {
        $target_file = $uploads_dir . basename($_FILES["file"]["name"]);
        $file_type = pathinfo($target_file, PATHINFO_EXTENSION);

        
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }

    
    if ($title && $date && $pastor && $description && $youtube_url) {
        $stmt = $conn->prepare("INSERT INTO serm (title, date, pastor, description, file_path, file_type, youtube_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $date, $pastor, $description, $file_path, $file_type, $youtube_url);

        
        if ($stmt->execute()) {
            echo "New sermon uploaded successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
}


if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); 

    
    $sql = "SELECT file_path FROM serm WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $file_path = $row['file_path'];

    
    if ($file_path && file_exists($file_path)) {
        unlink($file_path);
    }

    
    $sql = "DELETE FROM serm WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Sermon deleted successfully.";
    } else {
        echo "Error deleting sermon: " . $conn->error;
    }

    $stmt->close();
}


if (isset($_GET['file'])) {
    $file_name = basename($_GET['file']);
    $file_path = $uploads_dir . $file_name;

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        flush(); 
        readfile($file_path);
        exit;
    } else {
        echo "File not found.";
    }
}


$sql = "SELECT * FROM serm";
$result = $conn->query($sql);
$sermons = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sermons[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Sermon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/sermon.css">
</head>
<body>
<header class="topbar">
    <div class="logo-container">
        <h3>FAITH CHURCH</h3>
    </div>
    <div class="navigation-logout-container">
        <nav class="sidebar">
            <ul>
                <li><a href="../admin/homepage.php" class="nav-link">Home</a></li>
                <li><a href="../admin/sermons.php" class="nav-link">Sermons</a></li>
                <li><a href="../admin/event 2.php" class="nav-link">Events</a></li>
                <li><a href="../admin/re.php" class="nav-link">Resources</a></li>
            </ul>
        </nav>
        <div class="logout-container">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</header>

<div class="container">
    <div class="form-container">
        <form action="sermons.php" method="post" enctype="multipart/form-data">
        <h1>Upload Sermon</h1>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br><br>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required><br><br>

            <label for="pastor">Pastor:</label>
            <input type="text" id="pastor" name="pastor" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br><br>

            <label for="file">Upload Video:</label>
            <input type="file" id="file" name="file" accept="video/*"><br><br>

            <label for="youtube_url">YouTube URL:</label>
            <input type="text" id="youtube_url" name="youtube_url"><br><br>

            <button type="submit">Upload Sermon</button>
        </form>
    </div>
    <div class="table-container">
        <h2>Uploaded Sermons</h2>
        <?php if (count($sermons) > 0): ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Pastor's Name</th>
                    <th>File</th>
                    <th>YouTube Video</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($sermons as $sermon): ?>
                    <tr>
                        <td><?php echo $sermon['title']; ?></td>
                        <td><?php echo $sermon['date']; ?></td>
                        <td><?php echo $sermon['description']; ?></td>
                        <td><?php echo $sermon['pastor']; ?></td>
                        <td>
                        <?php if ($sermon['file_path']): ?>
                                <a href="sermons.php?file=<?php echo urlencode(basename($sermon['file_path'])); ?>">Download</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($sermon['youtube_url']): ?>
                                <iframe width="200" height="150" src="<?php echo str_replace('watch?v=', 'embed/', $sermon['youtube_url']); ?>" frameborder="0" allowfullscreen></iframe>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><a href="sermons.php?delete_id=<?php echo $sermon['id']; ?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No sermons uploaded yet.</p>
        <?php endif; ?>
    </div>
</div>
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section about">
            <h4>Contact Us</h4>
            <p><i class="fa fa-phone"></i> +254 713 010 881</p>
            <p><i class="fa fa-envelope"></i> faithchurch@gmail.com</p>
        </div>
        <div class="footer-section links">
            <h3>Helpful Links</h3>
            <ul>
                <li><a href="../user/about.html">About Us</a></li>
                <li><a href="../admin/help.html">Help</a></li>
            </ul>
        </div>
        <div class="footer-section links">
            <h3>Information</h3>
            <ul>
                <li><a href="../terms.html">Terms & Condition</a></li>
                <li><a href="../privacy.html">Privacy Policy</a></li>
            </ul>
        </div>
        <div class="footer-section subscribe">
            <h3>Subscribe More Info</h3>
            <form action="#">
                <input type="email" placeholder="Enter your Email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="social-media">
            <h3>Follow Us</h3>
            <a href="https://www.facebook.com" target="_blank" class="fa fa-facebook"></a>
            <a href="https://www.whatsapp.com" target="_blank" class="fa fa-whatsapp"></a>
            <a href="https://www.tiktok.com" target="_blank" class="fa fa-tiktok"></a>
        </div>
        <p>&copy; 2024 Faith Church. All Rights Reserved.</p>
    </div>
</footer>
</body>
</html>
