<?php
// Si le joueur clique sur "Commencer", on passe à l'étape 'jeu'
if (isset($_GET['commencer'])) {
    $_SESSION['etape'] = 'jeu';
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sauver PEACH !!!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('chateau.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 48px;
            text-shadow: 2px 2px 5px #000;
        }

        h2 {
            font-size: 28px;
            text-shadow: 1px 1px 3px #000;
        }

        p, ul {
            font-size: 18px;
            line-height: 1.6;
            text-shadow: 1px 1px 10px #000;
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #ff0000;
            color: white;
            font-size: 20px;
            border-radius: 10px;
            margin-top: 30px;

        }

        .button:hover {
            background-color: #cc0000;
        }

        .box {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 50px;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>Malheureusement Peach a encore était capturé par Bowser</h1>

        <h2>Objectif :</h2>
        <p>Vous devez explorer le labyrinthe du château et <strong>retrouver Peach</strong></p>

        <h2>Comment jouer</h2>
        <p>Collectez des clés, ouvrez les grilles et atteignez la sortie avant de vous perdre.</p>

        <h2>Règles</h2>
        <ul>
            <p>Chaque pas compte.</p>
            <p>Vous aurez besoin de clés pour ouvrir certaines grilles.</p>
            <p>Le jeu se termine lorsque vous atteignez la sortie.</p>
        </ul>

        <h2>Conseils</h2>
        <ul>
            <p>Évitez de prendre trop votre temps sinon Bowser risque de partir avec Peach</i>
        </ul>

        <a href="index.php?commencer=1" class="button">Commencer la partie</a>
    </div>
</body>
</html>
