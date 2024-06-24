<?php
session_start();

// Charger les événements existants
$eventsFile = 'events.json';
if (file_exists($eventsFile)) {
    $events = json_decode(file_get_contents($eventsFile), true);
} else {
    $events = [];
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Récupérer tous les projets
$projects = [];
foreach ($events as $user => $userEvents) {
    if ($role === 'responsable' || $user === $username) { // Le responsable voit tous les projets, sinon seuls les projets de l'utilisateur
        foreach ($userEvents as $event) {
            if (isset($event['projects'])) {
                foreach ($event['projects'] as $project) {
                    $project['createur'] = isset($event['createur']) ? $event['createur'] : 'Inconnu';
                    $project['nom'] = isset($event['title']) ? $event['title'] : 'N/A';
                    $project['Numéro'] = isset($event['tel_client']) ? $event['tel_client'] : 'N/A';
                    $project['Email'] = isset($event['email_client']) ? $event['email_client'] : 'N/A';
                    $project['lieu'] = isset($event['description']) ? $event['description'] : 'N/A'; // Remplacer location par description
                    $project['reprises'] = isset($project['reprises']) ? $project['reprises'] : [];
                    $project['date_relance'] = isset($project['date_relance']) ? $project['date_relance'] : 'N/A';
                    if (isDateInFourMonthInterval($project['date_relance'])) {
                        $projects[] = $project;
                    }
                }
            }
        }
    }
}

// Fonction pour vérifier si une date est dans l'intervalle de quatre mois
function isDateInFourMonthInterval($date) {
    if ($date === 'N/A') return false;
    
    $currentDate = new DateTime();
    $startDate = (clone $currentDate)->modify('-1 month')->modify('first day of this month');
    $endDate = (clone $currentDate)->modify('+12 months')->modify('last day of this month');
    $relanceDate = new DateTime($date);
    
    return $relanceDate >= $startDate && $relanceDate <= $endDate;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e;
            color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .table {
            background-color: #2a2a2a;
        }
        .table th, .table td {
            color: #ffffff;
            border: 1px solid #444444;
        }
        .table thead th {
            background-color: #333333;
        }
        .dropdown {
            background-color: #333333;
            color: #ffffff;
            border: 1px solid #444444;
        }
        .dropdown-item {
            color: #000000;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: #444444;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-custom:hover {
            background-color: #555555;
            text-decoration: none;
            color: #ffffff;
        }
        .modal-dark {
            background-color: #333333;
            color: #ffffff;
        }
        .modal-header, .modal-footer {
            border-bottom: 1px solid #444444;
            border-top: 1px solid #444444;
        }
        .modal-content {
            background-color: #333333;
            color: #ffffff;
        }
        .btn-close {
            background-color: #444444;
            border: none;
            color: #ffffff;
        }
        .btn-close:hover, .btn-close:focus {
            background-color: #555555;
        }
        .modal-body ul {
            list-style-type: none;
            padding: 0;
        }
        .modal-body ul li {
            margin-bottom: 10px;
        }
        .modal-body a {
            color: #1e90ff;
        }
        .modal-body a:hover {
            color: #63a4ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="btn-container">
            <a href="rapport.php" class="btn-custom">Retour à l'agenda</a>
            <a href="deconnexion.php" class="btn-custom">Déconnexion</a>
        </div>
        <h1 class="text-center">Liste des Projets</h1>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Catégorie
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="categoryDropdown">
                                <li><a class="dropdown-item" href="#" data-category="">Toutes</a></li>
                                <?php
                                // Récupérer les catégories uniques
                                $categories = array_unique(array_map(function($project) {
                                    return $project['categorie'];
                                }, $projects));
                                foreach ($categories as $category): ?>
                                    <li><a class="dropdown-item" href="#" data-category="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </th>
                    <th>Nom</th>
                    <th>Lieu</th>
                    <?php if ($role === 'responsable'): ?>
                    <th>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="creatorDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Créateur
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="creatorDropdownButton" id="creatorDropdown">
                                <li><a class="dropdown-item" href="#" data-creator="">Tous</a></li>
                                <?php
                                // Récupérer les créateurs uniques
                                $createurs = array_unique(array_map(function($project) {
                                    return $project['createur'];
                                }, $projects));
                                foreach ($createurs as $createur): ?>
                                    <li><a class="dropdown-item" href="#" data-creator="<?php echo htmlspecialchars($createur); ?>"><?php echo htmlspecialchars($createur); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </th>
                    <?php endif; ?>
                    <th>Date de relance</th>
                </tr>
            </thead>
            <tbody id="projectTable">
                <?php foreach ($projects as $project): ?>
                    <tr data-category="<?php echo htmlspecialchars($project['categorie']); ?>" data-creator="<?php echo htmlspecialchars($project['createur']); ?>" data-details="<?php echo htmlspecialchars(json_encode($project)); ?>">
                        <td><?php echo htmlspecialchars($project['categorie']); ?></td>
                        <td><?php echo htmlspecialchars($project['nom']); ?></td>
                        <td><?php echo htmlspecialchars($project['lieu']); ?></td>
                        <?php if ($role === 'responsable'): ?>
                        <td><?php echo htmlspecialchars($project['createur']); ?></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($project['date_relance']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalLabel">Détails du Projet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nom:</strong> <span id="modalNom"></span></p>
                    <p><strong>Lieu:</strong> <span id="modalLieu"></span></p>
                    <p><strong>Numéro:</strong> <span id="modalNumero"></span></p>
                    <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                    <p><strong>Observation:</strong> <span id="modalObservation"></span></p>
                    <p><strong>Date de relance:</strong> <span id="modalDateRelance"></span></p>
                    <p><strong>Reprises:</strong></p>
                    <ul id="modalReprises"></ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryDropdownItems = document.querySelectorAll('#categoryDropdown .dropdown-item');
            const creatorDropdownItems = document.querySelectorAll('#creatorDropdown .dropdown-item');
            const rows = document.querySelectorAll('#projectTable tr');
            let activeCategory = '';
            let activeCreator = '';

            categoryDropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    activeCategory = this.getAttribute('data-category');
                    filterTable();
                });
            });

            creatorDropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    activeCreator = this.getAttribute('data-creator');
                    filterTable();
                });
            });

            rows.forEach(row => {
                row.addEventListener('click', function() {
                    const project = JSON.parse(this.getAttribute('data-details'));
                    document.getElementById('modalNom').textContent = project.nom;
                    document.getElementById('modalLieu').textContent = project.lieu;
                    document.getElementById('modalNumero').textContent = project.Numéro;
                    document.getElementById('modalEmail').textContent = project.Email;
                    document.getElementById('modalObservation').textContent = project.observation;
                    document.getElementById('modalDateRelance').textContent = project.date_relance;

                    const reprisesList = document.getElementById('modalReprises');
                    reprisesList.innerHTML = '';
                    if (project.reprises.length > 0) {
                        project.reprises.forEach(reprise => {
                            const listItem = document.createElement('li');
                            listItem.innerHTML = `${reprise.reprise} (Photo: <a href="${reprise.photo}" target="_blank">Voir</a>, Fiche: <a href="${reprise.fiche}" target="_blank">Voir</a>)`;
                            reprisesList.appendChild(listItem);
                        });
                    } else {
                        reprisesList.innerHTML = '<li>Aucune reprise</li>';
                    }

                    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
                    modal.show();
                });
            });

            function filterTable() {
                rows.forEach(row => {
                    const categoryMatch = activeCategory === "" || row.getAttribute('data-category') === activeCategory;
                    const creatorMatch = activeCreator === "" || row.getAttribute('data-creator') === activeCreator;
                    if (categoryMatch && creatorMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
