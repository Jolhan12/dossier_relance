<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Charger les utilisateurs existants
    $usersFile = 'users.json';
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
    } else {
        $users = [];
    }

    // Définir le rôle : tous les utilisateurs sont des commerciaux sauf Damien qui est responsable
    $role = ($username === 'damien') ? 'responsable' : 'commercial';

    // Vérifier si l'utilisateur existe déjà
    if (isset($users[$username])) {
        $error = "Le nom d'utilisateur existe déjà.";
    } else {
        // Ajouter le nouvel utilisateur
        $users[$username] = [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role
        ];
        file_put_contents($usersFile, json_encode($users));

        // Connecter automatiquement l'utilisateur
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        header("Location: rapport.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e; /* Couleur de fond sombre */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #ffffff; /* Couleur du texte */
        }
        .container {
            max-width: 400px;
            padding: 20px;
            background-color: #2a2a2a; /* Couleur de fond de la boîte */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #ffffff; /* Couleur du texte */
        }
        input, button {
            width: calc(100% - 20px); /* Réduction de la largeur pour ajouter un espace de 10px de chaque côté */
            padding: 10px;
            margin: 10px auto; /* Centrage horizontal */
            border: 1px solid #444444; /* Bordure des champs */
            border-radius: 5px;
            font-size: 16px;
            display: block; /* Assurez-vous que les éléments d'entrée sont des blocs pour la marge automatique */
        }
        input {
            background-color: #333333; /* Couleur de fond des champs de saisie */
            color: #ffffff; /* Couleur du texte des champs de saisie */
        }
        button {
            background-color: #28a745; /* Couleur de fond du bouton */
            color: #ffffff; /* Couleur du texte du bouton */
            cursor: pointer;
        }
        button:hover {
            background-color: #218838; /* Couleur de fond du bouton au survol */
        }
        .error {
            color: #dc3545; /* Couleur du texte des messages d'erreur */
            margin-bottom: 15px;
        }
        .auth-link {
            display: block;
            margin-top: 15px;
            color: #28a745; /* Couleur du lien de connexion */
            text-decoration: none;
        }
        .auth-link:hover {
            text-decoration: underline;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <h2>Créer un compte</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="creation_compte.php">
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Créer un compte</button>
        </form>
        <a href="authentification.php" class="auth-link">Déjà un compte ? Connectez-vous ici</a>
    </div>
</body>
</html>
