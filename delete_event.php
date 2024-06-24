<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST['id'];
    $username = $_POST['username'];

    $eventsFile = 'events.json';
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true);

        if (isset($events[$username])) {
            $events[$username] = array_filter($events[$username], function($event) use ($eventId) {
                return $event['id'] != $eventId;
            });
        }

        file_put_contents($eventsFile, json_encode($events));
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File not found']);
    }
}
?>
