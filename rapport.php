<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: authentification.php");
    exit();
}

$username = isset($_SESSION['username']) ? ucfirst(strtolower($_SESSION['username'])) : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if ($username === null || $role === null) {
    header("Location: authentification.php");
    exit();
}

// Charger les utilisateurs existants
$usersFile = 'users.json';
if (file_exists($usersFile)) {
    $users = json_decode(file_get_contents($usersFile), true);
} else {
    $users = [];
}
error_log('Users loaded: ' . print_r($users, true));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agenda de <?php echo htmlspecialchars($username); ?></title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/fr.js'></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
.past-relance {
    background-color: red !important;
}

.relance-item {
    position: relative;
    padding-right: 30px; /* Espace supplémentaire pour la croix */
    margin-bottom: 10px; /* Espacement entre les éléments */
    background-color: #444444; /* Couleur de fond pour améliorer la lisibilité */
    border-radius: 5px; /* Coins arrondis pour un meilleur design */
    padding: 10px; /* Espacement interne pour un meilleur design */
}

.delete-relance {
    position: absolute;
    top: 50%;
    right: 5px;
    transform: translateY(-50%);
    cursor: pointer;
    color: white;
    font-weight: bold;
    background-color: grey;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.delete-relance:hover {
    background-color: darkgrey;
}


        .fc-event-red {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }

        .fc-event-green {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #ffffff !important;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e;
            margin: 0;
            padding: 20px;
            color: #ffffff;
        }
        .main-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .left-container {
            display: flex;
            flex-direction: column;
            width: 200px;
            margin-right: 20px; /* Espace entre les boutons et le calendrier */
        }
        .header-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #333333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px; /* Espace entre les boutons et les rappels de relances */
        }
        .sidebar {
            background-color: #333333;
            border: 1px solid #444444;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin-left: 20px; /* Espace entre le calendrier et les rappels de relances */
        }
        .container {
            flex: 1;
            max-width: 900px;
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .content {
            flex: 1;
        }
        .info-btn, .logout, .user-select {
            padding: 10px 20px;
            background-color: #444444;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
        }
        .info-btn:hover, .logout:hover, .user-select:hover {
            background-color: #555555;
            cursor: pointer;
        }
        h1 {
            padding: 10px 20px;
            background-color: #343a40;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            background-color: #333333;
            color: #ffffff;
            border: 1px solid #444444;
            border-radius: 10px;
        }
        .fc .fc-toolbar-title {
            color: #ffffff;
            font-size: 18px;
        }
        .fc-event-green {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #ffffff !important;
        }
        .fc-event-red {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
        }
        .fc .fc-button {
            background-color: #444444;
            border: 1px solid #555555;
            color: #ffffff;
            font-size: 14px;
        }
        .fc .fc-button:hover {
            background-color: #555555;
            cursor: pointer;
        }
        .fc-daygrid-event {
            background-color: #555555;
            border: 1px solid #666666;
            color: #ffffff;
            padding: 5px;
        }
        .user-select {
            padding: 10px;
            font-size: 16px;
            background-color: #444444;
            color: #ffffff;
            border: 1px solid #555555;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
        .user-select option {
            background-color: #444444;
            color: #ffffff;
        }
        .fc-event:hover {
            background-color: #555 !important;
            color: #fff !important;
            border-color: #777 !important;
            cursor: pointer;

        }
        .sidebar ul li:hover {
            background-color: #555;
            color: #fff;
            border-color: #777;
            cursor: pointer;

        }
        .add-event-btn {
            background-color: #343a40;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            text-align: center;
            position: fixed;
            bottom: 20px;
            right: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
        }
        .add-event-btn:hover {
            background-color: #23272b;
                cursor: pointer;

        }
        .add-event-btn i {
            font-size: 24px;
        }
        .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}

body.modal-open #calendar {
    pointer-events: none;
    filter: blur(5px);
}

.modal-content {
    background-color: #333333;
    color: #ffffff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #444444;
    width: 80%;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.close, .close-details, .close-info {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus,
.close-details:hover, .close-details:focus,
.close-info:hover, .close-info:focus {
    color: white;
    text-decoration: none;
    cursor: pointer;
}

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #555555;
            background-color: #444444;
            color: #ffffff;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #343a40;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #23272b;
        }
        .tab-buttons {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .tab-buttons button {
            flex: 1;
            padding: 10px 20px;
            cursor: pointer;
            background-color: #444;
            border: 1px solid #000;
            font-size: 16px;
            border-radius: 5px;
            margin: 0 5px;
        }
        .tab-buttons button.active {
            background-color: #555555;
            color: #ffffff;
            border: 1px solid #666666;
        }
        .sub-tab-buttons {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .sub-tab-buttons .tab-link {
            flex: 1;
            padding: 10px 20px;
            cursor: pointer;
            background-color: #444;
            border: 1px solid #000;
            font-size: 16px;
            border-radius: 5px;
            margin: 0 5px;
        }
        .sub-tab-buttons .tab-link.active {
            background-color: #555555;
            color: #ffffff;
            border: 1px solid #666666;
        }
        .calendar-container {
            display: none;
        }
        .tab {
            display: none;
        }
        .tab.active {
            display: block;
        }
        .calendar-container.active {
            display: block;
        }
        .relance-item {
    position: relative;
    padding-right: 30px; /* Espace supplémentaire pour la croix */
    margin-bottom: 10px; /* Espacement entre les éléments */
    background-color: #444444; /* Couleur de fond pour améliorer la lisibilité */
    border-radius: 5px; /* Coins arrondis pour un meilleur design */
    padding: 10px; /* Espacement interne pour un meilleur design */
}

.relance-item .complete-relance {
    position: absolute;
    top: 50%;
    right: 5px;
    transform: translateY(-50%);
    cursor: pointer;
    color: white;
    font-weight: bold;
    background-color: red;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.complete-relance:hover {
    background-color: darkred;
    color: white;
}



        .section-header {
            background-color: #333333;
            color: white;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 18px;
            border-radius: 10px 10px 0 0;
            border: 1px solid #444444;
        }
        .sidebar ul li {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #555555;
            border-radius: 0 0 10px 10px;
            background-color: #444444;
            color: #ffffff;
            list-style-type: none;
            margin-right: 20px;
        }

    </style>
</head>
<body>
<div class="main-container">
    <div class="left-container">
        <div class="header-left">
            <?php if ($role === 'responsable') { ?>
                <select id="userSelect" class="user-select">
                    <option value="<?php echo strtolower($username); ?>" selected>Mon Agenda</option>
                    <?php foreach ($users as $user => $userData) {
                        if (strtolower($user) !== strtolower($username) && strtolower($user) !== 'damien') {
                            echo '<option value="'. strtolower($user) .'">'. htmlspecialchars(ucfirst($user)) .'</option>';
                        }
                    } ?>
                </select>
            <?php } ?>
            <a href="projets.php" class="info-btn">Projets</a>
            <a href="deconnexion.php" class="info-btn">Déconnexion</a>
        </div>
    </div>
    <div class="container">
        <div class="header-right">
            <h1>Agenda de <?php echo htmlspecialchars($username); ?></h1>
        </div>
        <div class="content">
            <div id='calendar'></div>
        </div>
    </div>
    <div class="sidebar">
        <div class="section-header">Rappels de relances</div>
        <ul id="relanceList"></ul>
        </div>
</div>


<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Créer un événement</h2>
        <form id="eventForm" action="save_event.php" method="post" enctype="multipart/form-data">
        <input type="hidden" id="event_id" name="event_id">
        <label for="nom_client">Client :</label>
        <input type="text" id="nom_client" name="nom_client" required>
        <label for="secteur">Adresse / Code postal :</label>
        <input type="text" id="secteur" name="secteur" required>
        <label for="date_visite">Date de visite :</label>
        <input type="date" id="date_visite" name="date_visite" required>
        <label for="tel_client">Numéro de téléphone :</label>
        <input type="text" id="tel_client" name="tel_client">
        <label for="email_client">Email :</label>
        <input type="email" id="email_client" name="email_client">
        <label for="nombre_projet">Nombre de projet :</label>
        <input type="number" id="nombre_projet" name="nombre_projet" required min="0">
        <div id="projectTabs" class="tab-buttons"></div>
        <div id="projectFieldsContainer" class="project-container"></div>
        <input type="submit" value="Envoyer">
</form>

    </div>
</div>

<div id="eventDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close-details">&times;</span>
        <h2>Détails de l'événement</h2>
        <div id="eventDetailsContent"></div>
        <button id="editEventBtn">Modifier l'événement</button>
        <button id="deleteEventBtn">Supprimer l'événement</button>
    </div>
</div>
<div class="reprise-container" id="repriseContainer1_1">
    <label for="photo_reprise1_1">Photo de reprise 1 :</label>
    <input type="file" id="photo_reprise1_1" name="photo_reprise1_1" accept="image/*" data-project="1" data-reprise="1">
    <div id="additional_photos1_1"></div>
</div>

<button class="add-event-btn" id="addEventBtn"><i>+</i></button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var selectedDate = null;
        var selectedEventId = null;
        var currentUser = '<?php echo strtolower($username); ?>';

        function updateRelanceList(events) {
    var relanceList = document.getElementById('relanceList');
    relanceList.innerHTML = '';

    var today = new Date().toISOString().split('T')[0];
    var hiddenRelances = JSON.parse(localStorage.getItem('hiddenRelances')) || [];

    var relances = [];
    events.forEach(function(event) {
        if (event.projects && event.projects.length > 0) {
            event.projects.forEach(function(project) {
                if (project.date_relance && !project.completed) {
                    if (!hiddenRelances.find(relance => relance.eventId === event.id && relance.projectId === project.id)) {
                        relances.push({
                            client: event.title,
                            adresse: event.description,
                            date_relance: project.date_relance,
                            start: event.start,
                            eventId: event.id,
                            projectId: project.id
                        });
                    }
                }
            });
        }
    });

    relances.sort(function(a, b) {
        return new Date(a.date_relance) - new Date(b.date_relance);
    });

    relances.forEach(function(relance) {
        var listItem = document.createElement('li');
        listItem.classList.add('relance-item');
        if (new Date(relance.date_relance) < new Date(today)) {
            listItem.classList.add('past-relance');
        }
        listItem.innerHTML = `
            <span class="relance-date"><strong>Date de relance :</strong> ${relance.date_relance}</span><br>
            <span class="relance-client"><strong>Client :</strong> ${relance.client}</span><br>
            <span class="relance-adresse"><strong>Adresse :</strong> ${relance.adresse}</span>
            <button class="delete-relance" onclick="hideRelance('${relance.eventId}', '${relance.projectId}')">&times;</button>
        `;
        listItem.addEventListener('click', function() {
            calendar.gotoDate(new Date(relance.start));
            calendar.changeView('dayGridDay', new Date(relance.start));
        });

        relanceList.appendChild(listItem);
    });
}

// Assurez-vous que hideRelance est dans l'espace de noms global
window.hideRelance = function(eventId, projectId) {
    // Stocker l'état masqué dans le localStorage
    var hiddenRelances = JSON.parse(localStorage.getItem('hiddenRelances')) || [];
    hiddenRelances.push({ eventId: eventId, projectId: projectId });
    localStorage.setItem('hiddenRelances', JSON.stringify(hiddenRelances));

    // Mettre à jour la liste de relances
    updateRelanceList(calendar.getEvents());
};

// Fonction pour prévisualiser les images
function previewImages(event) {
    const projectNumber = event.target.getAttribute('data-project');
    const repriseNumber = event.target.getAttribute('data-reprise');
    const previewContainerId = `additional_photos${projectNumber}_${repriseNumber}`;
    const previewContainer = document.getElementById(previewContainerId);

    if (!previewContainer) {
        console.error(`Conteneur de prévisualisation non trouvé pour l'ID: ${previewContainerId}`);
        return;
    }

    const files = event.target.files;
    previewContainer.innerHTML = ''; // Clear any previous previews

    for (const file of files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.margin = '5px';
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}

document.addEventListener('change', function(event) {
    if (event.target.matches('input[type="file"]')) {
        previewImages(event);
    }
});


        function completeRelance(eventId, projectId) {
            if (eventId && projectId) {
                $.ajax({
                    url: 'complete_relance.php',
                    type: 'POST',
                    data: {
                        eventId: eventId,
                        projectId: projectId,
                        username: currentUser
                    },
                    success: function(response) {
                        try {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                calendar.refetchEvents();
                            } else {
                                alert("Erreur lors de la complétion de la relance: " + result.message);
                            }
                        } catch (e) {
                            console.error('JSON parsing error:', e, response);
                            alert('Erreur lors de la complétion de la relance: données JSON invalides');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        alert('Erreur lors de la complétion de la relance: ' + error);
                    }
                });
            } else {
                alert('Les identifiants de l\'événement et du projet sont manquants');
            }
        }





        function displayProjectDetails(project) {
    var detailsHtml = `
        <p><strong>Catégorie :</strong> ${project.categorie}</p>
        <p><strong>Observation :</strong> ${project.observation}</p>
        <p><strong>Date de relance :</strong> ${project.date_relance}</p>
    `;

    project.reprises.forEach(function(reprise, rIndex) {
        detailsHtml += `
            <p><strong>Reprise ${rIndex + 1} :</strong> ${reprise.reprise}</p>
            <p><strong>Photo de reprise ${rIndex + 1} :</strong> <a href="uploads/${reprise.photo}" target="_blank">Voir la photo</a></p>
            <p><strong>Fiche expertise de reprise ${rIndex + 1} :</strong> <a href="uploads/${reprise.fiche}" target="_blank">Voir la fiche</a></p>
        `;
    });

    return detailsHtml;
}

    function loadCalendar(username) {
    return new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridWeek',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: 'load_events.php',
                type: 'POST',
                data: { username: username },
                success: function(data) {
                    try {
                        var events = JSON.parse(data);
                        successCallback(events);
                        updateRelanceList(events);  // Mise à jour de la liste de relance
                    } catch (e) {
                        failureCallback(e);
                    }
                },
                error: function(xhr, status, error) {
                    failureCallback(error);
                }
            });
        },
        eventContent: function(arg) {
            let contentEl = document.createElement('div');
            contentEl.classList.add('fc-event-custom');
            contentEl.innerHTML = `
                <strong>${arg.event.title}</strong><br/>
                <i>${arg.event.extendedProps.description}</i>
            `;
            return { domNodes: [contentEl] };
        },
        eventClassNames: function(arg) {
            if (arg.event.extendedProps.completed) {
                return 'fc-event-completed';
            } else if (arg.event.extendedProps.projects && arg.event.extendedProps.projects.length > 0) {
                return 'fc-event-red';
            } else {
                return 'fc-event-green';
            }
        },
        eventClick: function(info) {
            var event = info.event;
            selectedEventId = event.id;

            // Afficher les détails de l'événement
            var detailsHtml = `
                <p><strong>Client :</strong> ${event.title}</p>
                <p><strong>Adresse / Code postal :</strong> ${event.extendedProps.description}</p>
                <p><strong>Numéro de téléphone :</strong> ${event.extendedProps.tel_client || ''}</p>
                <p><strong>Email :</strong> ${event.extendedProps.email_client || ''}</p>
                <p><strong>Date de visite :</strong> ${event.start.toISOString().split('T')[0]}</p>
            `;

            if (event.extendedProps.projects) {
                event.extendedProps.projects.forEach(function(project, index) {
                    detailsHtml += `
                        <p><strong>Projet ${index + 1} - Catégorie :</strong> ${project.categorie}</p>
                        <p><strong>Projet ${index + 1} - Observation :</strong> ${project.observation}</p>
                        <p><strong>Projet ${index + 1} - Date de relance :</strong> ${project.date_relance}</p>
                    `;

                    if (project.reprises) {
                        project.reprises.forEach(function(reprise, rIndex) {
                            detailsHtml += `
                                <p><strong>Reprise ${rIndex + 1} :</strong> ${reprise.reprise}</p>
                                <p><strong>Photo de reprise ${rIndex + 1} :</strong> <a href="${reprise.photo}" target="_blank">Voir la photo</a></p>
                                <p><strong>Fiche expertise de reprise ${rIndex + 1} :</strong> <a href="${reprise.fiche}" target="_blank">Voir la fiche</a></p>
                            `;
                        });
                    }
                });
            }

            document.getElementById('eventDetailsContent').innerHTML = detailsHtml;
            document.getElementById('eventDetailsModal').style.display = 'block';
            document.body.classList.add('modal-open');

            // Préremplir le formulaire de création
            document.getElementById('editEventBtn').onclick = function() {
                document.getElementById('nom_client').value = event.title;
                document.getElementById('secteur').value = event.extendedProps.description;
                document.getElementById('date_visite').value = event.start.toISOString().split('T')[0];
                document.getElementById('event_id').value = event.id;

                // Préremplir les projets
                var nombreProjet = event.extendedProps.projects ? event.extendedProps.projects.length : 0;
                document.getElementById('nombre_projet').value = nombreProjet;
                updateProjectFields(nombreProjet, event.extendedProps.projects);

                // Afficher le formulaire de création/modification
                document.getElementById('eventDetailsModal').style.display = 'none';
                document.getElementById('eventModal').style.display = 'block';
            }
        }
    });
}

        var calendar = loadCalendar(currentUser);
        calendar.render();

        <?php if ($role === 'responsable') { ?>
        document.getElementById('userSelect').addEventListener('change', function() {
            var selectedUser = this.value;
            currentUser = selectedUser;
            calendar.destroy(); // Détruire l'ancien calendrier
            calendar = loadCalendar(selectedUser);
            calendar.render(); // Afficher le nouveau calendrier
        });
        <?php } ?>

        function updateProjectFields(nombreProjet, projects = []) {
    var projectTabs = document.getElementById('projectTabs');
    var projectFieldsContainer = document.getElementById('projectFieldsContainer');
    projectTabs.innerHTML = '';
    projectFieldsContainer.innerHTML = '';

    if (nombreProjet > 0) {
        for (var j = 1; j <= nombreProjet; j++) {
            var project = projects[j - 1] || {};

            var projectTabButton = document.createElement('button');
            projectTabButton.type = 'button';
            projectTabButton.classList.add('tab-link');
            projectTabButton.dataset.tab = `project${j}`;
            projectTabButton.innerText = `Projet ${j}`;
            projectTabs.appendChild(projectTabButton);

            var projectDiv = document.createElement('div');
            projectDiv.classList.add('tab');
            projectDiv.id = `project${j}`;

            projectDiv.innerHTML = `
                <div>
                    <label for="categorie${j}">Projet ${j} - Catégorie :</label>
                    <select id="categorie${j}" name="categorie${j}" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <option value="Sol" ${project.categorie === 'Sol' ? 'selected' : ''}>Sol</option>
                        <option value="Fenaison" ${project.categorie === 'Fenaison' ? 'selected' : ''}>Fenaison</option>
                        <option value="Tracteur" ${project.categorie === 'Tracteur' ? 'selected' : ''}>Tracteur</option>
                        <option value="Epandage" ${project.categorie === 'Epandage' ? 'selected' : ''}>Epandage</option>
                        <option value="Broyage" ${project.categorie === 'Broyage' ? 'selected' : ''}>Broyage</option>
                        <option value="Autre" ${project.categorie === 'Autre' ? 'selected' : ''}>Autre</option>
                    </select>
                
                    <label for="observation${j}">Projet ${j} - Observation :</label>
                    <textarea id="observation${j}" name="observation${j}" rows="4" required>${project.observation || ''}</textarea>
                
                    <label for="date_relance${j}">Projet ${j} - Date de relance :</label>
                    <input type="date" id="date_relance${j}" name="date_relance${j}" required value="${project.date_relance || ''}">
                </div>
                <label for="nombre_reprise${j}">Projet ${j} - Nombre de reprise :</label>
                <input type="number" id="nombre_reprise${j}" name="nombre_reprise${j}" required min="0" data-project="${j}" value="${project.reprises ? project.reprises.length : 0}">
                
                <div class="sub-tab-buttons" id="repriseTabs${j}"></div>
                <div id="repriseFieldsContainer${j}" class="reprise-container"></div>
            `;
            
            projectFieldsContainer.appendChild(projectDiv);

            document.getElementById(`nombre_reprise${j}`).addEventListener('change', function() {
                var projectNumber = this.getAttribute('data-project');
                var nombreReprise = parseInt(this.value);
                var repriseTabs = document.getElementById(`repriseTabs${projectNumber}`);
                var repriseFieldsContainer = document.getElementById(`repriseFieldsContainer${projectNumber}`);
                repriseTabs.innerHTML = '';
                repriseFieldsContainer.innerHTML = '';

                if (nombreReprise > 0) {
                    for (var i = 1; i <= nombreReprise; i++) {
                        var reprise = project.reprises ? project.reprises[i - 1] : {};

                        var repriseTabButton = document.createElement('button');
                        repriseTabButton.type = 'button';
                        repriseTabButton.classList.add('tab-link');
                        repriseTabButton.dataset.tab = `project${projectNumber}_reprise${i}`;
                        repriseTabButton.innerText = `Reprise ${i}`;
                        repriseTabs.appendChild(repriseTabButton);

                        var column = document.createElement('div');
                        column.classList.add('tab');
                        column.id = `project${projectNumber}_reprise${i}`;

                        column.innerHTML = `
                            <label for="reprise${projectNumber}_${i}">Reprise ${i} :</label>
                            <input type="text" id="reprise${projectNumber}_${i}" name="reprise${projectNumber}_${i}" required value="${reprise.reprise || ''}">
                            
                            <label for="photo_reprise${projectNumber}_${i}">Photo de reprise ${i} :</label>
                            <input type="file" id="photo_reprise${projectNumber}_${i}" name="photo_reprise${projectNumber}_${i}" accept="image/*">
                            
                            <label for="fiche_expertise${projectNumber}_${i}">Fiche expertise de reprise ${i} :</label>
                            <input type="file" id="fiche_expertise${projectNumber}_${i}" name="fiche_expertise${projectNumber}_${i}" accept="image/*">
                        `;
                        repriseFieldsContainer.appendChild(column);
                    }

                    // Activer le premier sous-onglet
                    repriseTabs.children[0].classList.add('active');
                    repriseFieldsContainer.children[0].classList.add('active');
                }

                // Ajouter des événements aux boutons d'onglet des reprises
                document.querySelectorAll(`#repriseTabs${projectNumber} .tab-link`).forEach(button => {
                    button.addEventListener('click', function() {
                        var tabId = this.getAttribute('data-tab');

                        document.querySelectorAll(`#repriseFieldsContainer${projectNumber} .tab`).forEach(tab => {
                            tab.classList.remove('active');
                        });

                        document.getElementById(tabId).classList.add('active');

                        document.querySelectorAll(`#repriseTabs${projectNumber} .tab-link`).forEach(btn => {
                            btn.classList.remove('active');
                        });

                        this.classList.add('active');
                    });
                });
            });

            // Déclencher l'événement de changement pour remplir les champs des reprises
            document.getElementById(`nombre_reprise${j}`).dispatchEvent(new Event('change'));
        }

        // Activer le premier projet
        document.getElementById('project1').classList.add('active');
        projectTabs.children[0].classList.add('active');

        // Ajouter des événements aux boutons d'onglet des projets
        document.querySelectorAll('#projectTabs .tab-link').forEach(button => {
            button.addEventListener('click', function() {
                var tabId = this.getAttribute('data-tab');

                document.querySelectorAll('#projectFieldsContainer .tab').forEach(tab => {
                    tab.classList.remove('active');
                });

                document.getElementById(tabId).classList.add('active');

                document.querySelectorAll('#projectTabs .tab-link').forEach(btn => {
                    btn.classList.remove('active');
                });

                this.classList.add('active');
            });
        });
    }
}

document.getElementById('eventForm').onsubmit = function(e) {
        e.preventDefault();
        var title = document.getElementById('nom_client').value;
        var description = document.getElementById('secteur').value;
        var date = document.getElementById('date_visite').value;
        var eventId = document.getElementById('event_id').value;

        var newEvent = {
            id: eventId || Date.now().toString(),
            title: title,
            start: date,
            description: description,
            allDay: true,
            createur: currentUser  // Ajouter le champ "createur"

        };

        // Inclure les projets dans l'événement
        var nombreProjet = document.getElementById('nombre_projet').value;
        newEvent.projects = [];
        for (var j = 1; j <= nombreProjet; j++) {
            var project = {
                categorie: document.getElementById(`categorie${j}`).value,
                observation: document.getElementById(`observation${j}`).value,
                date_relance: document.getElementById(`date_relance${j}`).value,
                reprises: []
            };

            var nombreReprise = document.getElementById(`nombre_reprise${j}`).value;
            for (var i = 1; i <= nombreReprise; i++) {
                project.reprises.push({
                    reprise: document.getElementById(`reprise${j}_${i}`).value,
                    photo: '',
                    fiche: ''
                });
            }

            newEvent.projects.push(project);
        }

        var formData = new FormData();
        formData.append('event', JSON.stringify(newEvent));
        formData.append('username', currentUser);

        // Ajoutez les fichiers au FormData
        for (var j = 1; j <= nombreProjet; j++) {
            var nombreReprise = document.getElementById(`nombre_reprise${j}`).value;
            for (var i = 1; i <= nombreReprise; i++) {
                var photoInput = document.getElementById(`photo_reprise${j}_${i}`);
                var ficheInput = document.getElementById(`fiche_expertise${j}_${i}`);
                if (photoInput.files[0]) {
                    formData.append(`photo_reprise${j}_${i}`, photoInput.files[0]);
                }
                if (ficheInput.files[0]) {
                    formData.append(`fiche_expertise${j}_${i}`, ficheInput.files[0]);
                }
            }
        }

        $.ajax({
            url: 'save_event.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                calendar.refetchEvents();
                document.getElementById('eventForm').reset();
                document.getElementById('date_visite').value = selectedDate;
                document.getElementById('eventModal').style.display = 'none';
                document.body.classList.remove('modal-open');
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });
    };

    // Ajout d'événements change pour les champs de fichier pour la prévisualisation des images
    document.addEventListener('change', function(event) {
        if (event.target.matches('input[type="file"]')) {
            const projectNumber = event.target.getAttribute('data-project');
            const repriseNumber = event.target.getAttribute('data-reprise');
            previewImages(event, projectNumber, repriseNumber);
        }
    });


        // Modal logic
        var modal = document.getElementById('eventModal');
        var detailsModal = document.getElementById('eventDetailsModal');
        var span = document.getElementsByClassName('close')[0];
        var spanDetails = document.getElementsByClassName('close-details')[0];

        span.onclick = function() {
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

spanDetails.onclick = function() {
    detailsModal.style.display = 'none';
    document.body.classList.remove('modal-open');
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
    if (event.target == detailsModal) {
        detailsModal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}


        // Show modal when add event button is clicked
        document.getElementById('addEventBtn').onclick = function() {
            var today = new Date().toISOString().split('T')[0];
            if (!selectedDate) {
                selectedDate = today;
            }
            document.getElementById('date_visite').value = selectedDate;
            document.getElementById('eventModal').style.display = 'block';
        }

        // Number of projects handling
        document.getElementById('nombre_projet').addEventListener('change', function() {
            var nombreProjet = parseInt(this.value);
            var projectTabs = document.getElementById('projectTabs');
            var projectFieldsContainer = document.getElementById('projectFieldsContainer');
            projectTabs.innerHTML = '';
            projectFieldsContainer.innerHTML = '';

            if (nombreProjet > 0) {
                for (var j = 1; j <= nombreProjet; j++) {
                    var projectTabButton = document.createElement('button');
                    projectTabButton.type = 'button';
                    projectTabButton.classList.add('tab-link');
                    projectTabButton.dataset.tab = `project${j}`;
                    projectTabButton.innerText = `Projet ${j}`;
                    projectTabs.appendChild(projectTabButton);

                    var projectDiv = document.createElement('div');
                    projectDiv.classList.add('tab');
                    projectDiv.id = `project${j}`;

                    projectDiv.innerHTML = `
                        <div>
                            <label for="categorie${j}">Projet ${j} - Catégorie :</label>
                            <select id="categorie${j}" name="categorie${j}" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="Sol">Sol</option>
                                <option value="Fenaison">Fenaison</option>
                                <option value="Tracteur">Tracteur</option>
                                <option value="Epandage">Epandage</option>
                                <option value="Broyage">Broyage</option>
                                <option value="Distribution">Distribution</option>
                                <option value="Transport">Transport</option>
                                <option value="Manutention">Manutention</option>
                                <option value="Autre">Autre</option>
                            </select>
                        
                            <label for="observation${j}">Projet ${j} - Observation :</label>
                            <textarea id="observation${j}" name="observation${j}" rows="4" required></textarea>
                        
                            <label for="date_relance${j}">Projet ${j} - Date de relance :</label>
                            <input type="date" id="date_relance${j}" name="date_relance${j}" required>
                        </div>
                        <label for="nombre_reprise${j}">Projet ${j} - Nombre de reprise :</label>
                        <input type="number" id="nombre_reprise${j}" name="nombre_reprise${j}" required min="0" data-project="${j}">
                        
                        <div class="sub-tab-buttons" id="repriseTabs${j}"></div>
                        <div id="repriseFieldsContainer${j}" class="reprise-container"></div>
                    `;
                    
                    projectFieldsContainer.appendChild(projectDiv);

                    document.getElementById(`nombre_reprise${j}`).addEventListener('change', function() {
                        var projectNumber = this.getAttribute('data-project');
                        var nombreReprise = parseInt(this.value);
                        var repriseTabs = document.getElementById(`repriseTabs${projectNumber}`);
                        var repriseFieldsContainer = document.getElementById(`repriseFieldsContainer${projectNumber}`);
                        repriseTabs.innerHTML = '';
                        repriseFieldsContainer.innerHTML = '';
                        
                        if (nombreReprise > 0) {
                            for (var i = 1; i <= nombreReprise; i++) {
                                var repriseTabButton = document.createElement('button');
                                repriseTabButton.type = 'button';
                                repriseTabButton.classList.add('tab-link');
                                repriseTabButton.dataset.tab = `project${projectNumber}_reprise${i}`;
                                repriseTabButton.innerText = `Reprise ${i}`;
                                repriseTabs.appendChild(repriseTabButton);

                                var column = document.createElement('div');
                                column.classList.add('tab');
                                column.id = `project${projectNumber}_reprise${i}`;
                                
                                column.innerHTML = `
                                    <label for="reprise${projectNumber}_${i}">Reprise ${i} :</label>
                                    <input type="text" id="reprise${projectNumber}_${i}" name="reprise${projectNumber}_${i}" required>
                                    
                                    <label for="photo_reprise${projectNumber}_${i}">Photo de reprise ${i} :</label>
                                    <input type="file" id="photo_reprise${projectNumber}_${i}" name="photo_reprise${projectNumber}_${i}" accept="image/*">
                                    <button type="button" onclick="addPhotoField(${projectNumber}, ${i})">Ajouter une photo</button>
                                    <div id="additional_photos${projectNumber}_${i}"></div>
                                    
                                    <label for="fiche_expertise${projectNumber}_${i}">Fiche expertise de reprise ${i} :</label>
                                    <input type="file" id="fiche_expertise${projectNumber}_${i}" name="fiche_expertise${projectNumber}_${i}" accept="application/pdf">
                                    <button type="button" onclick="addPhotoField(${projectNumber}, ${i})">Ajouter une photo</button>
                                    <div id="additional_photos${projectNumber}_${i}"></div>
                                `;
                                repriseFieldsContainer.appendChild(column);
                            }

                            // Activer le premier sous-onglet
                            repriseTabs.children[0].classList.add('active');
                            repriseFieldsContainer.children[0].classList.add('active');
                        }

                        // Ajouter des événements aux boutons d'onglet des reprises
                        document.querySelectorAll(`#repriseTabs${projectNumber} .tab-link`).forEach(button => {
                            button.addEventListener('click', function() {
                                var tabId = this.getAttribute('data-tab');
                                
                                document.querySelectorAll(`#repriseFieldsContainer${projectNumber} .tab`).forEach(tab => {
                                    tab.classList.remove('active');
                                });

                                document.getElementById(tabId).classList.add('active');

                                document.querySelectorAll(`#repriseTabs${projectNumber} .tab-link`).forEach(btn => {
                                    btn.classList.remove('active');
                                });

                                this.classList.add('active');
                            });
                        });
                    });
                }

                // Activer le premier projet
                document.getElementById('project1').classList.add('active');
                projectTabs.children[0].classList.add('active');
            }

            // Ajouter des événements aux boutons d'onglet des projets
            document.querySelectorAll('#projectTabs .tab-link').forEach(button => {
                button.addEventListener('click', function() {
                    var tabId = this.getAttribute('data-tab');
                    
                    document.querySelectorAll('#projectFieldsContainer .tab').forEach(tab => {
                        tab.classList.remove('active');
                    });

                    document.getElementById(tabId).classList.add('active');

                    document.querySelectorAll('#projectTabs .tab-link').forEach(btn => {
                        btn.classList.remove('active');
                    });

                    this.classList.add('active');
                });
            });
        });

        document.getElementById('eventForm').onsubmit = function(e) {
    e.preventDefault();
    var title = document.getElementById('nom_client').value;
    var description = document.getElementById('secteur').value;
    var date = document.getElementById('date_visite').value;
    var eventId = document.getElementById('event_id').value;

    var newEvent = {
    id: eventId || Date.now().toString(),
    title: title,
    start: date,
    description: description,
    allDay: true,
    createur: currentUser,  // Ajouter le champ "createur"
    tel_client: document.getElementById('tel_client').value, // Ajouter le champ "tel_client"
    email_client: document.getElementById('email_client').value // Ajouter le champ "email_client"
};

    // Inclure les projets dans l'événement
    var nombreProjet = document.getElementById('nombre_projet').value;
    newEvent.projects = [];
    for (var j = 1; j <= nombreProjet; j++) {
        var project = {
            categorie: document.getElementById(`categorie${j}`).value,
            observation: document.getElementById(`observation${j}`).value,
            date_relance: document.getElementById(`date_relance${j}`).value,
            reprises: []
        };

        var nombreReprise = document.getElementById(`nombre_reprise${j}`).value;
        for (var i = 1; i <= nombreReprise; i++) {
            project.reprises.push({
                reprise: document.getElementById(`reprise${j}_${i}`).value,
                photo: document.getElementById(`photo_reprise${j}_${i}`).value,
                fiche: document.getElementById(`fiche_expertise${j}_${i}`).value
            });
        }

        newEvent.projects.push(project);
    }

    function saveNewEvent() {
        $.ajax({
            url: 'save_event.php',
            type: 'POST',
            data: {
                event: JSON.stringify(newEvent),
                username: currentUser
            },
            success: function(response) {
                calendar.refetchEvents();
                document.getElementById('eventForm').reset();
                document.getElementById('date_visite').value = selectedDate;
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });
    }

    // Suppression de l'ancien événement si l'événement est modifié
    if (eventId) {
        $.ajax({
            url: 'delete_event.php',
            type: 'POST',
            data: {
                id: eventId,
                username: currentUser
            },
            success: function(deleteResponse) {
                saveNewEvent();
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });
    } else {
        saveNewEvent();
    }
}

        document.getElementById('deleteEventBtn').onclick = function() {
        if (confirm("Êtes-vous sûr de vouloir supprimer cet événement ?")) {
            $.ajax({
                url: 'delete_event.php',
                type: 'POST',
                data: {
                    id: selectedEventId,
                    username: currentUser
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        calendar.refetchEvents();
                        detailsModal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                    } else {
                        alert("Erreur lors de la suppression de l'événement: " + result.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
        }
    };
        document.getElementById('editEventBtn').onclick = function() {
        detailsModal.style.display = 'none';
        modal.style.display = 'block';  // Réutiliser la modale de création pour la modification
    }
    });
</script>



</body>
</html>
