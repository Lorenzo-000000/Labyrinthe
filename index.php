<?php
session_start();

// Si aucune étape n'est définie, on commence au début
if (!isset($_SESSION['etape'])) {
    $_SESSION['etape'] = 'debut';
}

// Redirection selon l’étape actuelle
switch ($_SESSION['etape']) {
    case 'debut':
        include 'debut.php';
        break;

    case 'jeu':
        include 'labyrinthe.php';
        break;

    case 'fin':
        include 'fin.php';
        break;

    default:
        echo "Erreur : étape inconnue.";
}
?>