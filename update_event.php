<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event = json_decode($_POST['event'], true);
    $username = $_POST['username'];

    $eventsFile = 'events.json';
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fichier des événements non trouvé']);
        exit();
    }

    if (isset($events[$username])) {
        foreach ($events[$username] as &$e) {
            if ($e['id'] == $event['id']) {
                $e = $event;
                file_put_contents($eventsFile, json_encode($events));
                echo json_encode(['status' => 'success']);
                exit();
            }
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'Événement non trouvé']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
}
?>
