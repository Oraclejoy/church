<?php
include '../config.php';


function getLatestSermon($conn) {
    $sql = "SELECT * FROM serm ORDER BY date DESC LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}


function getAllSermons($conn) {
    $sql = "SELECT * FROM serm ORDER BY date DESC";
    $result = $conn->query($sql);
    $sermons = [];
    while ($row = $result->fetch_assoc()) {
        $sermons[] = $row;
    }
    return $sermons;
}


function convertYouTubeURL($url) {
    $embedURL = '';
    if (strpos($url, 'watch?v=') !== false) {
        $embedURL = str_replace('watch?v=', 'embed/', $url);
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $embedURL = str_replace('youtu.be/', 'youtube.com/embed/', $url);
    } else {
        return $url;
    }
    return $embedURL . '?controls=0';
}



$searchResults = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM serm WHERE date LIKE '%$search%' OR pastor LIKE '%$search%'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }
}


if (isset($_GET['file'])) {
    $file_id = $_GET['file'];
    

    $sql = "SELECT file_path FROM serm WHERE id = '$file_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_path = $row['file_path'];

        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "File not found.";
    }
}


$latestSermon = getLatestSermon($conn);
$allSermons = getAllSermons($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sermons</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/sermonuser.css">
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
    <h1>Sermons</h1>

    <div class="latest-sermon">
        <h2>Latest Sermon</h2>
        <?php if ($latestSermon): ?>
            <h3><?php echo htmlspecialchars($latestSermon['title']); ?></h3>
            <p>Date: <?php echo htmlspecialchars($latestSermon['date']); ?></p>
            <p>Pastor: <?php echo htmlspecialchars($latestSermon['pastor']); ?></p>
            <p>Description: <?php echo htmlspecialchars($latestSermon['description']); ?></p>
            <?php if ($latestSermon['youtube_url']): ?>
                <iframe width="560" height="315" src="<?php echo htmlspecialchars(convertYouTubeURL($latestSermon['youtube_url'])); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <?php elseif ($latestSermon['file_type'] == 'mp4' || $latestSermon['file_type'] == 'avi'): ?>
                <video width="320" height="240" controls>
                    <source src="<?php echo htmlspecialchars($latestSermon['file_path']); ?>" type="video/<?php echo htmlspecialchars($latestSermon['file_type']); ?>">
                    Your browser does not support the video tag.
                </video>
            <?php elseif ($latestSermon['file_type'] == 'mp3'): ?>
                <audio controls>
                    <source src="<?php echo htmlspecialchars($latestSermon['file_path']); ?>" type="audio/<?php echo htmlspecialchars($latestSermon['file_type']); ?>">
                    Your browser does not support the audio element.
                </audio>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($latestSermon['file_path']); ?>" target="_blank">Read Sermon</a>
            <?php endif; ?>
            <a href="?file=<?php echo $latestSermon['id']; ?>">Download</a>
        <?php else: ?>
            <p>No latest sermon found.</p>
        <?php endif; ?>
    </div>

    <div class="search-bar">
        <form action="sermonuser.php" method="get">
            <input type="text" name="search" placeholder="Search by date (YYYY-MM-DD) or pastor's name" required>
            <button type="submit">Search</button>
        </form>
    </div>

    <?php if (count($searchResults) > 0): ?>
        <div class="search-results">
            <h2>Search Results</h2>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Pastor</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($searchResults as $sermon): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sermon['title']); ?></td>
                        <td><?php echo htmlspecialchars($sermon['date']); ?></td>
                        <td><?php echo htmlspecialchars($sermon['pastor']); ?></td>
                        <td><?php echo htmlspecialchars($sermon['description']); ?></td>
                        <td>
                            <?php if ($sermon['youtube_url']): ?>
                                <iframe width="200" height="113" src="<?php echo htmlspecialchars(convertYouTubeURL($sermon['youtube_url'])); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <?php elseif (in_array($sermon['file_type'], ['mp4', 'avi'])): ?>
                                <video width="100" height="80" controls>
                                    <source src="<?php echo htmlspecialchars($sermon['file_path']); ?>" type="video/<?php echo htmlspecialchars($sermon['file_type']); ?>">
                                    Your browser does not support the video tag.
                                </video>
                            <?php elseif ($sermon['file_type'] == 'mp3'): ?>
                                <audio controls>
                                    <source src="<?php echo htmlspecialchars($sermon['file_path']); ?>" type="audio/<?php echo htmlspecialchars($sermon['file_type']); ?>">
                                    Your browser does not support the audio element.
                                </audio>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($sermon['file_path']); ?>" target="_blank">Read Sermon</a>
                            <?php endif; ?>
                            <a href="?file=<?php echo $sermon['id']; ?>">Download</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <div class="recent-sermons">
        <h2>All Sermons</h2>
        <ul>
            <?php foreach ($allSermons as $sermon): ?>
                <li>
                    <h3><?php echo htmlspecialchars($sermon['title']); ?></h3>
                    <p>Date: <?php echo htmlspecialchars($sermon['date']); ?></p>
                    <p>Pastor: <?php echo htmlspecialchars($sermon['pastor']); ?></p>
                    <p>Description: <?php echo htmlspecialchars($sermon['description']); ?></p>
                    <?php if ($sermon['youtube_url']): ?>
                        <iframe width="560" height="315" src="<?php echo htmlspecialchars(convertYouTubeURL($sermon['youtube_url'])); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <?php elseif ($sermon['file_type'] == 'mp4' || $sermon['file_type'] == 'avi'): ?>
                        <video width="320" height="240" controls>
                            <source src="<?php echo htmlspecialchars($sermon['file_path']); ?>" type="video/<?php echo htmlspecialchars($sermon['file_type']); ?>">
                            Your browser does not support the video tag.
                        </video>
                    <?php elseif ($sermon['file_type'] == 'mp3'): ?>
                        <audio controls>
                            <source src="<?php echo htmlspecialchars($sermon['file_path']); ?>" type="audio/<?php echo htmlspecialchars($sermon['file_type']); ?>">
                            Your browser does not support the audio element.
                        </audio>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($sermon['file_path']); ?>" target="_blank">Read Sermon</a>
                    <?php endif; ?>
                    <a href="?file=<?php echo $sermon['id']; ?>">Download</a>
                </li>
            <?php endforeach; ?>
        </ul>
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
