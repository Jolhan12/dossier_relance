<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de contact</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tab {
            display: none;
        }

        .tab.active {
            display: block;
        }

        .tab-buttons {
            display: flex;
            margin-bottom: 20px;
        }

        .tab-buttons button {
            flex: 1;
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: background-color 0.3s;
            border-radius: 12px; /* Ajoute des coins arrondis */
            margin: 0 5px; /* Ajoute un espace entre les onglets */
        }

        .tab-buttons button:hover {
            background-color: #ddd;
        }

        .tab-buttons button.active {
            background-color: #fff;
            border-bottom: 1px solid #fff;
        }

        .sub-tab-buttons {
            display: flex;
            margin-bottom: 20px;
        }

        .sub-tab-buttons button {
            flex: 1;
            padding: 10px;
            cursor: pointer;
            background-color: #f4f4f4;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: background-color 0.3s;
            border-radius: 12px; /* Ajoute des coins arrondis */
            margin: 0 5px; /* Ajoute un espace entre les sous-onglets */
        }

        .sub-tab-buttons button:hover {
            background-color: #ddd;
        }

        .sub-tab-buttons button.active {
            background-color: #fff;
            border-bottom: 1px solid #fff;
        }
    </style>
</head>
<body>
    <h1>Formulaire de contact</h1>

    <div class="container">
        <form action="save_data.php" method="post" enctype="multipart/form-data" id="contactForm">
            <label for="nom_client">Client :</label>
            <input type="text" id="nom_client" name="nom_client" required>
            
            <label for="secteur">Secteur :</label>
            <input type="text" id="secteur" name="secteur" required>
            
            <label for="representant">Représentant :</label>
            <select id="representant" name="representant" required>
                <option value="">Sélectionnez un représentant</option>
                <option value="Damien">Damien</option>
                <option value="Florian">Florian</option>
                <option value="Rayan">Rayan</option>
            </select>
            
            <label for="date_visite">Date de visite :</label>
            <input type="date" id="date_visite" name="date_visite" required>
            
            <label for="nombre_projet">Nombre de projet :</label>
            <input type="number" id="nombre_projet" name="nombre_projet" required min="0">
            
            <div id="projectTabs" class="tab-buttons"></div>
            <div id="projectFieldsContainer" class="project-container"></div>
            
            <input type="submit" value="Envoyer">
        </form>
    </div>

    <script>
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
    </script>
</body>
</html>
