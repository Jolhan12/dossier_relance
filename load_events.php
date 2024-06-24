<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    $eventsFile = 'events.json';
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true);
    } else {
        $events = [];
    }

    if (isset($events[$username])) {
        foreach ($events[$username] as &$event) {
            // Vérifiez si les champs tel_client et email_client existent, sinon, les définir par défaut
            if (!isset($event['tel_client'])) {
                $event['tel_client'] = '';
            }
            if (!isset($event['email_client'])) {
                $event['email_client'] = '';
            }

            // Assurez-vous également que chaque projet a un créateur défini
            if (isset($event['projects'])) {
                foreach ($event['projects'] as &$project) {
                    if (!isset($project['createur'])) {
                        $project['createur'] = $username;
                    }
                }
            }
        }
        error_log("Événements trouvés pour l'utilisateur $username: " . json_encode($events[$username])); // Log des événements
        echo json_encode(array_values($events[$username])); // Convertir en tableau indexé pour FullCalendar
    } else {
        echo json_encode([]);
    }
}
?>
