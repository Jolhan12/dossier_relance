<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event = json_decode($_POST['event'], true);
    $username = $_POST['username'];

    // Validation des champs obligatoires
    if (empty($event['title']) || empty($event['start']) || empty($event['description'])) {
        echo json_encode(['status' => 'error', 'message' => 'Les champs Client, Adresse/Code postal et Date de visite sont obligatoires.']);
        exit();
    }

    if (!isset($event['projects']) || !is_array($event['projects'])) {
        echo json_encode(['status' => 'error', 'message' => 'Le champ Nombre de projet est obligatoire.']);
        exit();
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($event['projects'] as &$project) {
        if (empty($project['categorie']) || empty($project['observation']) || empty($project['date_relance'])) {
            echo json_encode(['status' => 'error', 'message' => 'Les champs Catégorie, Observation et Date de relance sont obligatoires pour chaque projet.']);
            exit();
        }

        foreach ($project['reprises'] as &$reprise) {
            $photoField = 'photo_reprise' . $project['id'] . '_' . $reprise['id'];
            if (isset($_FILES[$photoField])) {
                $photo = $_FILES[$photoField];
                if ($photo['error'] == UPLOAD_ERR_OK) {
                    $photoName = basename($photo['name']);
                    $targetFile = $uploadDir . $photoName;
                    if (move_uploaded_file($photo['tmp_name'], $targetFile)) {
                        $reprise['photo'] = $targetFile;
                    }
                }
            }

            $ficheField = 'fiche_expertise' . $project['id'] . '_' . $reprise['id'];
            if (isset($_FILES[$ficheField])) {
                $fiche = $_FILES[$ficheField];
                if ($fiche['error'] == UPLOAD_ERR_OK) {
                    $ficheName = basename($fiche['name']);
                    $targetFile = $uploadDir . $ficheName;
                    if (move_uploaded_file($fiche['tmp_name'], $targetFile)) {
                        $reprise['fiche'] = $targetFile;
                    }
                }
            }
        }
    }

    $eventsFile = 'events.json';
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true);
    } else {
        $events = [];
    }

    if (!isset($events[$username])) {
        $events[$username] = [];
    }

    $eventExists = false;
    foreach ($events[$username] as &$e) {
        if ($e['id'] == $event['id']) {
            $e = $event;
            $eventExists = true;
            break;
        }
    }

    if (!$eventExists) {
        $events[$username][] = $event;
    }

    file_put_contents($eventsFile, json_encode($events));

    echo json_encode(['status' => 'success']);
}
?>
