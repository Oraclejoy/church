<?php
include '../config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resource']) && isset($_FILES['cover_image'])) {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    
    $file = $_FILES['resource'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileType = $file['type'];
    $fileContent = file_get_contents($fileTmpName); 

    
    $coverImage = $_FILES['cover_image'];
    $coverImageTmpName = $coverImage['tmp_name'];
    $coverImageData = file_get_contents($coverImageTmpName); 

    
    move_uploaded_file($coverImageTmpName, 'uploads/' . $coverImage['name']);

    $stmt = $conn->prepare("INSERT INTO resources (category, title, description, file_name, file_type, file_content, cover_image_data) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $category, $title, $description, $fileName, $fileType, $fileContent, $coverImageData);
    $stmt->execute();
    $stmt->close();

    echo "Resource uploaded successfully.";
}


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT file_name, file_type, file_content FROM resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($fileName, $fileType, $fileContent);
    $stmt->fetch();
    $stmt->close();

    if ($fileName) {
        header("Content-Description: File Transfer");
        header("Content-Type: $fileType");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . strlen($fileContent));
        echo $fileContent;
        exit;
    } else {
        echo "File not found.";
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo "Resource deleted successfully.";
}


$stmt = $conn->prepare("SELECT id, category, title, cover_image_data FROM resources");
$stmt->execute();
$resources = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$categories = ["children", "family", "Young_adults", "articles", "health", "financial","videos/audio","songs"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/re.css">
    <title>Library Resources</title>
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
            <h1>Upload Resource</h1>
            <form action="re.php" method="POST" enctype="multipart/form-data">
                <label for="category">Category:</label>
                <select name="category" id="category" required>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category ?>"><?= ucfirst($category) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea>
                <label for="resource">Resource File:</label>
                <input type="file" name="resource" id="resource" required>
                <label for="cover_image">Cover Image:</label>
                <input type="file" name="cover_image" id="cover_image" required>
                <button type="submit">Upload Resource</button>
            </form>
        </div>
        <div class="resources-container">
            <h2>Uploaded Resources</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Cover Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT id, category, title, cover_image_data FROM resources");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars(ucfirst($row['category'])) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><img src="data:image/jpeg;base64,<?= base64_encode($row['cover_image_data']) ?>" alt="Cover Image" style="width:50px;height:50px;"></td>
                            <td>
                                <a href="re.php?id=<?= $row['id'] ?>">Download</a>
                                <a href="re.php?delete=<?= $row['id'] ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile;
                    $stmt->close();
                    ?>
                </tbody>
            </table>
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
