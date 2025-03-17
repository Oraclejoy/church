<?php
include '../config.php';
session_start();

// Handle testimony submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $testimony = $_POST['testimony'];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $sql = "INSERT INTO testimonies (name, image_path, testimony) VALUES ('$name', '$target_file', '$testimony')";
    if (mysqli_query($conn, $sql)) {
        $message = "Testimony added successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Fetch services from database
$services_sql = "SELECT * FROM services ORDER BY start_time ASC";
$services_result = mysqli_query($conn, $services_sql);

// Fetch announcements from database
$announcements_sql = "SELECT * FROM announcements ORDER BY title  DESC";
$announcements_result = mysqli_query($conn, $announcements_sql);

// Fetch upcoming events from database
$today = date('Y-m-d');
$events_sql = "SELECT name, date, description, venue FROM event WHERE date >= '$today' ORDER BY date ASC";
$events_result = $conn->query($events_sql);

// Fetch testimonials from database
$testimonials_sql = "SELECT * FROM testimonies ORDER BY submission_date DESC";
$testimonials_result = mysqli_query($conn, $testimonials_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faith Church</title>
    <link rel="stylesheet" href="../css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMl5LVKNKc7JrG14pK2Qe7iC5pRWfX1YJBk8HRX" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
</head>
<body>
<header class="topbar">
    <div class="logo-container">
        <h3>FAITH CHURCH</h3>
    </div>
    <div class="navigation-logout-container">
        <nav class="sidebar">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#announcements">Announcements</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <div class="cta-buttons">
                    <button id="giveTestimonyBtn" class="btn">Give Testimony</button>
                </div>
            </ul>
        </nav>
    </div>
</header>
<div class="banner">
    <video autoplay loop muted plays-inline>
        <source src="../images/SUNMISOLA LIVE- PROPHETIC WORSHIP MEDLEY.mp4" type="video/mp4">
    </video>
    <div class="content">
        <h1>FAITH CHURCH</h1>
        <div>
            <button type="button" onclick="location.href='../register_form.php'">Join Us</button>
        </div>
    </div>
</div>

<main>
    <section id="services">
        <div class="services-heading">
            <h2>Services</h2>
        </div>
        <div class="service-container">
            <?php
            if (mysqli_num_rows($services_result) > 0) {
                while ($row = mysqli_fetch_assoc($services_result)) {
                    echo "<div class='service-box'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['start_time']) . "</p>";
                    echo "<p>" . htmlspecialchars($row['end_time']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No services available.</p>";
            }
            ?>
        </div>
    </section>

    <section id="about" class="about_section">
    <div class="about">
        <div class="about_text_container">
            <h1 class="about_title">About Us</h1>
            <p class="about_text">
            we are a community of believers who are passionate about loving God, loving people, and making a difference in the world. We believe that Jesus Christ is the Son of God and our Savior, and we strive to follow His teachings in our daily lives
            </p>
            <div class="read_bt_1">
                <a href="../user/about.html">Read More</a>
            </div>
        </div>
        <div class="about_img_container">
            <img src="../images/2ae4a9b27a6ba4981328fcf06cc91d1a.jpg" alt="About Image" class="img-fluid about_img">
        </div>
    </div>
</section>

    <section id="announcements">
        <div class="announcements-heading">
            <h2>Announcements</h2>
        </div>
        <div class="announcement-container">
            <?php
            if (mysqli_num_rows($announcements_result) > 0) {
                while ($row = mysqli_fetch_assoc($announcements_result)) {
                    echo "<div class='announcement-box'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    
                    echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No announcements available.</p>";
            }
            ?>
        </div>
    </section>

    <section id="events">
        <div class="events-heading">
            <h2>Upcoming Events</h2>
        </div>
        <div class="event-container">
            <?php
            if ($events_result->num_rows > 0) {
                while ($row = $events_result->fetch_assoc()) {
                    echo "<div class='event-box'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['date']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<p>Venue: " . htmlspecialchars($row['venue']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No upcoming events.</p>";
            }
            ?>
        </div>
    </section>

    <section id="testimonials" class="testimonials">
        <div class="container">
            <h2 class="section-head"><span>Testimonials</span></h2>
            <ul class="bxslider">
                <?php
                if (mysqli_num_rows($testimonials_result) > 0) {
                    while ($row = mysqli_fetch_assoc($testimonials_result)) {
                        echo '<li>';
                        echo '<div class="testimonial-item">';
                        echo '<div class="img_cont"><img src="' . $row['image_path'] . '" alt="' . $row['name'] . '"></div>';
                        echo '<div class="content">';
                        echo '<p class="testimony-text">' . $row['testimony'] . '</p>';
                        echo '<div class="testimony">';
                        echo '<p class="person-name">' . $row['name'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</li>';
                    }
                } else {
                    echo "<p>No testimonials available.</p>";
                }
                ?>
            </ul>
        </div>
    </section>
</main>

<div id="testimonyModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Give Testimony</h2>
        <form id="testimonyForm" action="" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" required>
            <label for="testimony">Testimony:</label>
            <textarea id="testimony" name="testimony" required></textarea>
            <button type="submit">Submit Testimony</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
<script>
    $(document).ready(function(){
        $('.bxslider').bxSlider({
            auto: true,
            mode: 'fade',
            captions: true,
            slideWidth: 600
        });
    });

    const modal = document.getElementById("testimonyModal");
    const btn = document.getElementById("giveTestimonyBtn");
    const span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

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
