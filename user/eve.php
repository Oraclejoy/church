<?php

include '../config.php';



$events = [];
$recentEvents = [];
$upcomingEvents = [];
$allEvents = [];

$sql = "SELECT id, name, venue, date, description, poster, speakers, start_time, end_time FROM event ORDER BY date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['speakers'] = json_decode($row['speakers'], true);
        $allEvents[] = $row;
        
        $eventDate = strtotime($row['date']);
        $currentDate = time();

        if ($eventDate > $currentDate) {
            $upcomingEvents[] = $row;
        } else {
            $recentEvents[] = $row;
        }
    }

    
    $recentEvents = array_slice($recentEvents, 0, 3);
}


$endedEvents = [];
$currentDate = date('Y-m-d'); 
$sql = "SELECT id, name FROM event WHERE date < '$currentDate' ORDER BY date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $endedEvents[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/event_user.css">
    <style>
        .event-timer {
            font-size: 1.2em;
            color: #FF0000;
            margin-top: 10px;
        }
        .event-section {
            margin-bottom: 50px;
        }
        .event-section h2 {
            margin-bottom: 20px;
        }
        .event-buttons {
            margin-top: 10px;
        }
        .gallery-container, .testimonials-container, .testimonial-form-container {
            display: none;
        }
        .gallery-item, .testimonial-item {
            margin-bottom: 10px;
        }
    </style>
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
<h1>Events</h1>
    <div class="event-section" id="upcomingEvents">
        <h2>Upcoming Events</h2>
        <div id="upcomingEventList">
            <?php if (count($upcomingEvents) > 0): ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event">
                        <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                        <p><strong>Start Time:</strong> <?php echo htmlspecialchars($event['start_time']); ?></p>
                        <p><strong>End Time:</strong> <?php echo htmlspecialchars($event['end_time']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        <?php if (!empty($event['poster'])): ?>
                            <img src="<?php echo htmlspecialchars($event['poster']); ?>" alt="Event Poster" style="width: 100px; height: 100px;">
                        <?php endif; ?>
                        <?php if (!empty($event['speakers'])): ?>
                            <h4>Speakers:</h4>
                            <ul>
                                <?php foreach ($event['speakers'] as $speaker): ?>
                                    <li>
                                        <?php if (!empty($speaker['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($speaker['image']); ?>" alt="<?php echo htmlspecialchars($speaker['name']); ?>" style="width: 50px; height: 50px;">
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($speaker['name']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <div class="event-timer" data-date="<?php echo htmlspecialchars($event['date']); ?>"></div>
                        <div class="event-buttons">
                            <button onclick="window.location.href='../user/gallery.php?event_id=<?php echo htmlspecialchars($event['id']); ?>'">View Gallery</button>
                        </div>
                        <div id="gallery-<?php echo htmlspecialchars($event['id']); ?>" class="gallery-container"></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming events found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="event-section" id="recentEvents">
    <h2>Recent Events</h2>
    <div id="recentEventList">
        <?php if (count($recentEvents) > 0): ?>
            <?php foreach ($recentEvents as $event): ?>
                <div class="event">
                    <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                    <p><strong>Start Time:</strong> <?php echo htmlspecialchars($event['start_time']); ?></p>
                    <p><strong>End Time:</strong> <?php echo htmlspecialchars($event['end_time']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    <?php if (!empty($event['poster'])): ?>
                        <img src="<?php echo htmlspecialchars($event['poster']); ?>" alt="Event Poster" style="width: 100px; height: 100px;">
                    <?php endif; ?>
                    <?php if (!empty($event['speakers'])): ?>
                        <h4>Speakers:</h4>
                        <ul>
                            <?php foreach ($event['speakers'] as $speaker): ?>
                                <li>
                                    <?php if (!empty($speaker['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($speaker['image']); ?>" alt="<?php echo htmlspecialchars($speaker['name']); ?>" style="width: 50px; height: 50px;">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($speaker['name']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="event-buttons">
                        <button onclick="window.location.href='../user/gallery.php?event_id=<?php echo $event['id']; ?>'">View Gallery</button>
                    </div>
                    <div id="gallery-<?php echo $event['id']; ?>" class="gallery-container"></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recent events found.</p>
        <?php endif; ?>
    </div>
    </div>

    <div class="event-section" id="allEvents">
        <h2>All Events</h2>
        <div id="allEventList">
            <?php if (count($allEvents) > 0): ?>
                <?php foreach ($allEvents as $event): ?>
                    <div class="event">
                        <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                        <p><strong>Start Time:</strong> <?php echo htmlspecialchars($event['start_time']); ?></p>
                        <p><strong>End Time:</strong> <?php echo htmlspecialchars($event['end_time']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        <?php if (!empty($event['poster'])): ?>
                            <img src="<?php echo htmlspecialchars($event['poster']); ?>" alt="Event Poster" style="width: 100px; height: 100px;">
                        <?php endif; ?>
                        <?php if (!empty($event['speakers'])): ?>
                            <h4>Speakers:</h4>
                            <ul>
                                <?php foreach ($event['speakers'] as $speaker): ?>
                                    <li>
                                        <?php if (!empty($speaker['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($speaker['image']); ?>" alt="<?php echo htmlspecialchars($speaker['name']); ?>" style="width: 50px; height: 50px;">
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($speaker['name']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <div class="event-buttons">
                        <button onclick="window.location.href='../user/gallery.php?event_id=<?php echo $event['id']; ?>'">View Gallery</button>
                            
                        </div>
                        <div id="gallery-<?php echo $event['id']; ?>" class="gallery-container"></div>
                        
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No events found.</p>
            <?php endif; ?>
        </div>
    </div>

    

    
</div>
    <script>
       function showGallery(eventId) {
    const galleryContainer = document.getElementById(`gallery-${eventId}`);
    galleryContainer.style.display = galleryContainer.style.display === 'block' ? 'none' : 'block';
    
}



    



function updateTimers() {
    const timers = document.querySelectorAll('.event-timer');
    timers.forEach(timer => {
        const eventDate = new Date(timer.getAttribute('data-date')).getTime();
        const now = new Date().getTime();
        const distanceStart = eventDate - now;
        const distanceEnd = eventDate + new Date(timer.getAttribute('data-end-time')).getTime() - now;

        if (distanceStart > 0) {
            const days = Math.floor(distanceStart / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distanceStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distanceStart % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distanceStart % (1000 * 60)) / 1000);

            timer.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s until start`;
        } else if (distanceEnd < 0) {
            timer.innerHTML = "Event has ended";
        } else {
            const days = Math.floor(distanceEnd / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distanceEnd % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distanceEnd % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distanceEnd % (1000 * 60)) / 1000);

            timer.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s until end`;
        }
    });
}


setInterval(updateTimers, 1000);


updateTimers();


    </script>
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
</body>
</html>
