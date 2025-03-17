<?php

include('../config.php'); 


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


$categories = ["children", "family", "Young_adults", "articles", "health", "financial","videos/audio","songs"];


$category = isset($_GET['category']) ? $_GET['category'] : '';
$resourcesQuery = $category ? "SELECT * FROM resources WHERE category='$category'" : "SELECT * FROM resources";
$resourcesResult = mysqli_query($conn, $resourcesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Resources</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/resource_user.css">
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
                    <li><a href="../user/sermonuser.php" class="nav-link">Sermons</a></li>
                    <li><a href="../user/eve.php" class="nav-link">Events</a></li>
                    <li><a href="../user/resource_user.php" class="nav-link">Resources</a></li>
                </ul>
            </nav>
            <div class="logout-container">
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </header>
<div class="container">
    <aside>
        <h2>RESOURCES</h2>
        <ul>
            <li><a href="../user/resource_user.php">All</a></li>
            <?php foreach($categories as $cat): ?>
                <li><a href="../user/resource_user.php?category=<?php echo urlencode($cat); ?>"><?php echo htmlspecialchars($cat); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </aside>
    <main>
        <h2><?php echo $category ? htmlspecialchars($category) : 'All Resources'; ?></h2>
        <div class="resources">
            <?php while($row = mysqli_fetch_assoc($resourcesResult)): ?>
                <div class="resource-item">
                    <div class="cover-image" style="background-image: url('data:image/jpeg;base64,<?php echo base64_encode($row['cover_image_data']); ?>');"></div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    
                    <a href="#" class="read-more" data-full-description="<?php echo htmlspecialchars($row['description']); ?>">Description</a>
                    <a href="read_resource.php?id=<?php echo $row['id']; ?>" class="read-button">View</a>
                    <a href="resource_user.php?id=<?php echo $row['id']; ?>" class="download-button">Download</a>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>
<footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h4>Contact Us</h4>
                <p><i class="fa fa-phone"></i> +254 713 010 881</p>
                <p><i class="fa fa-envelope"></i> faithchurch@gmail.com</p>
            </div>
            <div class="footer-section links">
                <h3>Information</h3>
                <ul>
                    <li><a href="../user/about.html">About Us</a></li>
                    <li><a href="../admin/help.html">Help</a></li>
                    
                    
                </ul>
            </div>
            <div class="footer-section links">
                <h3>Helpful Links</h3>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const readMoreLinks = document.querySelectorAll('.read-more');

    readMoreLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            alert(link.getAttribute('data-full-description'));
        });
    });
});
</script>
</body>
</html>
