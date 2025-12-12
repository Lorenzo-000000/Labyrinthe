<?php
session_start();

echo "<h1>Bravo ! Fin du jeu 🎉</h1>";

// Quand la partie se termine
if(isset($_SESSION['start_time'])) {
    $end_time = time(); // temps de fin
    $elapsed = $end_time - $_SESSION['start_time']; // différence en secondes

    echo "La partie a duré $elapsed secondes.";
}

session_destroy(); // Réinitialiser si tu veux recommencer
echo "<form method='get' action=''>
        <button type='submit' name='reset' value='1'>Recommencer une nouvelle partie</button>
      </form>";
?>
