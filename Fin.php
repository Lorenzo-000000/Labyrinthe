<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Félicitations !</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/fin.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            text-align: center;
            padding-top: 100px;
        }
        h1 {
            font-size: 40px;
            text-shadow: 2px 2px 5px #000;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #ff0000;
            color: white;
            font-size: 20px;
            text-decoration: none;
            border-radius: 10px;
            margin-top: 600px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <h1>Félicitations ! Peach est sauvée ! 🎉</h1>
    <a href="index.php?rejouer=1" class="button">Rejouer</a>
</body>
</html>

<?php
// Si le joueur clique sur "Rejouer", on reset la session
if (isset($_GET['rejouer'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>