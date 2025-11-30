<?php
session_start();

$db = new SQLite3("labyrinthe.db");

// Initialisation de l'inventaire

if (!isset($_SESSION["cle"])) {
    $_SESSION["cle"] = false; // Pas de clé au début
}


// Récupérer la position actuelle

if (isset($_GET["position"])) {
    $position = (int)$_GET["position"];
	if (isset($_GET["grille_ouverte"])) {
        $_SESSION["cle"] = false; // consommation de la clé si grille ouverte
    }
} else {
    // Requête SQL : récupérer la case de départ
    $req = $db->query("SELECT id FROM couloir WHERE type = 'depart' LIMIT 1");
    $row = $req->fetchArray(SQLITE3_ASSOC);
    $position = $row["id"];
}


// Récupérer le type de la case actuelle

$info = $db->query("SELECT type FROM couloir WHERE id = $position")->fetchArray(SQLITE3_ASSOC);
$type_actuel = $info["type"] ?? "inconnu";

// Si on marche sur une clé, on l'ajoute à l'inventaire
if ($type_actuel === "cle") {
    $_SESSION["cle"] = true;
}


// Fonctions utilitaires

function normaliserDirection($dir) {
    $dir = trim($dir);
    $dir = strtoupper($dir);
    if (!in_array($dir, ["N","S","E","O"])) return "Secret";
    return $dir;
}

function directionFull($dir) {
    $map = ["N" => "NORD", "S" => "SUD", "E" => "EST", "O" => "OUEST"];
    return $map[$dir] ?? "SECRET";
}


// Récupérer les couloirs accessibles depuis la position actuelle

$sql = "
SELECT
    CASE WHEN couloir1 = :position THEN couloir2 ELSE couloir1 END AS couloir_dispo,
    CASE WHEN couloir1 = :position THEN position2 ELSE position1 END AS direction,
    type AS type_passage
FROM passage
WHERE couloir1 = :position OR couloir2 = :position;
";

$stmt = $db->prepare($sql);
$stmt->bindValue(":position", $position, SQLITE3_INTEGER);
$result = $stmt->execute();


// Affichage

echo "<h1>Position : Couloir $position (type : $type_actuel)</h1>";

// Inventaire
echo $_SESSION["cle"] // Ajt compteur cle + cle utilisable une fois
    ? "<p><b>Inventaire : Clé disponible 🔑</b></p>"
    : "<p><b>Inventaire : aucune clé</b></p>";

// Déplacements possibles

echo "<h2>Déplacements possibles :</h2><ul>";

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

    $couloir_dispo = $row["couloir_dispo"];
    $direction = normaliserDirection($row["direction"]);
    $direction_text = directionFull($direction);
    $type_passage = $row["type_passage"];

    //  Passage bloqué si grille et pas de clé
    if ($type_passage === "grille" && $_SESSION["cle"] === false) {
        echo "<li>Le couloir $couloir_dispo est bloqué  (grille, pas de clé)</li>";
        continue;
    }

    //  Passage grille avec clé → consommation de la clé
    if ($type_passage === "grille" && $_SESSION["cle"] === true) {
        echo "<li>Le couloir $couloir_dispo est vérrouiller, veux tu utilisé ta clé? ($direction_text)
                <a href='?position=$couloir_dispo&grille_ouverte=1'>Aller</a>
              </li>";
        continue;
    }

    //  Passage libre
    echo "<li>Le couloir $couloir_dispo est disponible, y aller ? ($direction_text)
            <a href='?position=$couloir_dispo'>Aller</a>
          </li>";

}

echo "</ul>";


// Bouton recommencer

echo "<form method='get' action=''>
        <button type='submit' name='reset' value='1'>Recommencer une nouvelle partie</button>
      </form>";


// Gestion du reset

if (isset($_GET['reset'])) {
    session_destroy(); // supprime toutes les données de session
    header("Location: ".$_SERVER['PHP_SELF']); // recharge la page pour une nouvelle partie
    exit;
}
