<?php
$eventsFile = 'events.json';

if (file_exists($eventsFile)) {
    // Charger les événements existants
    $events = json_decode(file_get_contents($eventsFile), true);

    foreach ($events as $user => &$userEvents) {
        foreach ($userEvents as &$event) {
            if (isset($event['projects'])) {
                foreach ($event['projects'] as &$project) {
                    // Vérifier si le projet a déjà un identifiant, sinon en générer un
                    if (!isset($project['id']) || empty($project['id'])) {
                        $project['id'] = uniqid();
                    }
                }
            }
        }
    }

    // Enregistrer les modifications dans le fichier
    file_put_contents($eventsFile, json_encode($events, JSON_PRETTY_PRINT));

    echo "Les identifiants uniques ont été ajoutés aux projets existants.";
} else {
    echo "Le fichier events.json n'existe pas.";
}

