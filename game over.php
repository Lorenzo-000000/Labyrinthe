<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Game Over</title>
    <style>
 /* ===== Style général ===== */
body {
    margin: 0;
    padding: 0;
    font-family: 'Press Start 2P', Arial, sans-serif;
    background: url('images/game over.png') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: flex-end; /* texte vers le bas */
    align-items: center;
    height: 100vh;
    text-align: center;
    padding-bottom: 50px; /* espace depuis le bas */
}

/* Titre Game Over */
h1 {
    font-size: 48px;
    color: #ffcc00;
    text-shadow: 3px 3px 0 #000;
    margin-bottom: 20px;
}

/* Message */
p {
    font-size: 20px;
    color: #ff4444;
    text-shadow: 2px 2px 0 #000;
    margin-bottom: 30px;
}

/* Bouton recommencer */
a {
    display: inline-block;
    background: #ff0000;
    color: #fff;
    font-weight: bold;
    padding: 12px 24px;
    border-radius: 50px;
    text-decoration: none;
    box-shadow: 0 4px 0 #800000;
    transition: all 0.2s;
}

a:hover {
    background: #cc0000;
    transform: scale(1.1);
}

/* Exemple pour ajouter un effet sur le texte */
h1, p {
    animation: pulse 1.4s infinite;
}

/* ===== Nouveau : style Mario message ===== */
.mario-message {
    font-size: 18px;
    color: #ffff66; /* jaune rétro */
    text-shadow: 2px 2px #000; /* contour noir */
    margin-bottom: 30px;
}
    </style>
</head>
<body>
    <h1>GAME OVER</h1>
    <div class="mario-message">
        Mario n’a pas eu le temps… 🍄
    </div>
    <a href="index.php">Recommencer</a>
</body>
</html>
