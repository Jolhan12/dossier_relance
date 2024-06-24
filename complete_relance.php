<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? null;
    $eventId = $_POST['eventId'] ?? null;
    $projectId = $_POST['projectId'] ?? null;

    if ($username && $eventId && $projectId) {
        $eventsFile = 'events.json';
        if (file_exists($eventsFile)) {
            $events = json_decode(file_get_contents($eventsFile), true);

            if (isset($events[$username])) {
                foreach ($events[$username] as &$event) {
                    if (isset($event['id']) && $event['id'] == $eventId) {
                        foreach ($event['projects'] as &$project) {
                            if (isset($project['id']) && $project['id'] == $projectId) {
                                $project['completed'] = true; // Marquer la relance comme complétée
                                // Enregistrer le fichier après la modification
                                file_put_contents($eventsFile, json_encode($events));
                                echo json_encode(['status' => 'success']);
                                exit;
                            }
                        }
                    }
                }
                echo json_encode(['status' => 'error', 'message' => 'Projet non trouvé']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Utilisateur non trouvé']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Fichier des événements non trouvé']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
}
?>
