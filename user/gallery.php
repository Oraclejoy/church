<?php
include '../config.php';

$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';

$events_query = "SELECT * FROM event ORDER BY date DESC";
$events_result = $conn->query($events_query);

$gallery_items = [];
if ($event_id) {
    $gallery_query = "SELECT * FROM gallery WHERE event_id = $event_id";
    $gallery_result = $conn->query($gallery_query);
    while ($row = $gallery_result->fetch_assoc()) {
        $gallery_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Gallery</title>
    <style>
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
        }
        .gallery-item {
            margin: 10px;
        }
        .gallery-item img, .gallery-item video {
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>
<body>
    <h1>Event Gallery</h1>

    <form method="GET" action="gallery.php">
        <label for="event_id">Select Event:</label>
        <select name="event_id" id="event_id" onchange="this.form.submit()">
            <option value="">--Select Event--</option>
            <?php while ($event = $events_result->fetch_assoc()): ?>
                <option value="<?php echo $event['id']; ?>" <?php echo $event['id'] == $event_id ? 'selected' : ''; ?>>
                    <?php echo $event['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <div class="gallery-container">
        <?php foreach ($gallery_items as $item): ?>
            <div class="gallery-item">
                <?php if (strpos($item['file_type'], 'image') !== false): ?>
                    <img src="<?php echo $item['file_path']; ?>" alt="Gallery Image">
                <?php elseif (strpos($item['file_type'], 'video') !== false): ?>
                    <video controls>
                        <source src="<?php echo $item['file_path']; ?>" type="<?php echo $item['file_type']; ?>">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>


