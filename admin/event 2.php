<?php

include '../config.php';


$name = isset($_POST['name']) ? $_POST['name'] : '';
$venue = isset($_POST['venue']) ? $_POST['venue'] : '';
$date = isset($_POST['date']) ? $_POST['date'] : '';
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$poster = isset($_POST['poster']) ? $_POST['poster'] : '';


$sql = "INSERT INTO event (name, venue, date, start_time, end_time, description, poster) VALUES ('$name', '$venue', '$date', '$start_time', '$end_time', '$description', '$poster')";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = htmlspecialchars($_POST['name']);
        $venue = htmlspecialchars($_POST['venue']);
        $date = htmlspecialchars($_POST['date']);
        $start_time = htmlspecialchars($_POST['start_time']);
        $end_time = htmlspecialchars($_POST['end_time']);
        $description = htmlspecialchars($_POST['description']);

        $poster = '';
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] == UPLOAD_ERR_OK) {
            $posterData = file_get_contents($_FILES['poster']['tmp_name']);
            $posterType = mime_content_type($_FILES['poster']['tmp_name']);
            $poster = 'data:' . $posterType . ';base64,' . base64_encode($posterData);
        }

        $speakers = [];
        if (isset($_POST['speaker_name']) && is_array($_POST['speaker_name'])) {
            foreach ($_POST['speaker_name'] as $index => $speaker_name) {
                if (!empty($speaker_name)) {
                    $speaker_image = '';
                    if (isset($_FILES['speaker_image']['tmp_name'][$index]) && $_FILES['speaker_image']['error'][$index] == UPLOAD_ERR_OK) {
                        $imageData = file_get_contents($_FILES['speaker_image']['tmp_name'][$index]);
                        $imageType = mime_content_type($_FILES['speaker_image']['tmp_name'][$index]);
                        $speaker_image = 'data:' . $imageType . ';base64,' . base64_encode($imageData);
                    }
                    $speakers[] = ['name' => htmlspecialchars($speaker_name), 'image' => $speaker_image];
                }
            }
        }

        $speakersJson = json_encode($speakers);
        $sql = "INSERT INTO event (name, venue, date, start_time, end_time, description, poster, speakers) VALUES ('$name', '$venue', '$date', '$start_time', '$end_time', '$description', '$poster', '$speakersJson')";

        if ($conn->query($sql) === TRUE) {
            echo "Event added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $eventId = intval($_POST['event_id']);
        $sql = "DELETE FROM event WHERE id = $eventId";
        if ($conn->query($sql) === TRUE) {
            echo "Event deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    
    } elseif (isset($_POST['action']) && $_POST['action'] == 'add_gallery') {
        $eventId = intval($_POST['event_id']);
        if (isset($_FILES['gallery_item']) && $_FILES['gallery_item']['error'] == UPLOAD_ERR_OK) {
            $fileData = file_get_contents($_FILES['gallery_item']['tmp_name']);
            $fileType = mime_content_type($_FILES['gallery_item']['tmp_name']);
            $filePath = 'data:' . $fileType . ';base64,' . base64_encode($fileData);
            $sql = "INSERT INTO gallery (event_id, file_path, file_type) VALUES ($eventId, '$filePath', '$fileType')";
            if ($conn->query($sql) === TRUE) {
                echo "Gallery item added successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

    
    }
}


$events = [];
$sql = "SELECT id, name, venue, date, start_time, end_time, description, poster, speakers FROM event";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['speakers'] = json_decode($row['speakers'], true);
        $events[] = $row;
    }
}


$past_events = [];
$sql = "SELECT id, name FROM event WHERE date < CURDATE()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $past_events[] = $row;
    }
}



$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <link rel="stylesheet" href="../css/event2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .event-timer {
            font-size: 1.2em;
            color: #FF0000;
            margin-top: 10px;
        }
        .speaker {
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
    <div class="eventform">
        
        <h1>Add Event</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <label for="name">Event Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="venue">Venue:</label>
            <input type="text" id="venue" name="venue" required>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required>

            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="poster">Poster:</label>
            <input type="file" id="poster" name="poster">

            <div id="speakers">
                <h3>Speakers</h3>
                <button type="button" onclick="addSpeaker()">Add Speaker</button>
            </div>

            <button type="submit">Add Event</button>
        </form>
    </div>
    <div class="eventContainer">
    
    <div id="eventList">
        <?php if (count($events) > 0): ?>
            <?php foreach ($events as $index => $event): ?>
                <div class="event" data-index="<?php echo $index; ?>" style="display: none;">
                    <h3><?php echo htmlspecialchars($event['name']); ?></h3>
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($event['start_time']) . ' - ' . htmlspecialchars($event['end_time']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    <?php if ($event['poster']): ?>
                        <img src="<?php echo htmlspecialchars($event['poster']); ?>" alt="Poster" style="max-width: 200px;">
                    <?php endif; ?>

                    <?php if (!empty($event['speakers'])): ?>
                        <h4>Speakers:</h4>
                        <ul>
                            <?php foreach ($event['speakers'] as $speaker): ?>
                                <li>
                                    <?php if ($speaker['image']): ?>
                                        <img src="<?php echo htmlspecialchars($speaker['image']); ?>" alt="Speaker Image" style="max-width: 50px;">
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($speaker['name']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <form action="" method="post" onsubmit="return confirm('Are you sure you want to delete this event?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit">Delete Event</button>
                    </form>

                    <h4>Add Gallery Item:</h4>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_gallery">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <label for="gallery_item">Gallery Item:</label>
                        <input type="file" id="gallery_item" name="gallery_item" required>
                        <button type="submit">Add to Gallery</button>
                    </form>

                    
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
    <button id="prevButton" onclick="prevEvent()">Previous</button>
    <button id="nextButton" onclick="nextEvent()">Next</button>

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
        
        let currentIndex = 0;
        const events = document.querySelectorAll('.event');

        function showEvent(index) {
            events.forEach((event, i) => {
                event.style.display = i === index ? 'block' : 'none';
            });
            updateButtons();
        }

        function nextEvent() {
            if (currentIndex < events.length - 1) {
                currentIndex++;
                showEvent(currentIndex);
            }
        }

        function prevEvent() {
            if (currentIndex > 0) {
                currentIndex--;
                showEvent(currentIndex);
            }
        }

        function updateButtons() {
            document.getElementById('prevButton').disabled = currentIndex === 0;
            document.getElementById('nextButton').disabled = currentIndex === events.length - 1;
        }

        
        showEvent(currentIndex);
        updateButtons();

        
        function addSpeaker() {
            const speakersDiv = document.getElementById('speakers');
            const speakerDiv = document.createElement('div');
            speakerDiv.className = 'speaker';

            const nameLabel = document.createElement('label');
            nameLabel.innerText = 'Speaker Name:';
            speakerDiv.appendChild(nameLabel);

            const nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = 'speaker_name[]';
            nameInput.required = true;
            speakerDiv.appendChild(nameInput);

            const imageLabel = document.createElement('label');
            imageLabel.innerText = 'Speaker Image:';
            speakerDiv.appendChild(imageLabel);

            const imageInput = document.createElement('input');
            imageInput.type = 'file';
            imageInput.name = 'speaker_image[]';
            speakerDiv.appendChild(imageInput);

            speakersDiv.appendChild(speakerDiv);
        }
    </script>
    </body>
</html>