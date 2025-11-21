<?php
session_start();

$db = new SQLite3("labyrinthe.db");

if (!isset($_SESSION["cle"])) {
    $_SESSION["cle"] = false;
}

if (isset($_GET["position"])) {
    $position = (int)$_GET["position"];
    if (isset($_GET["grid_opened"])) {
        $_SESSION["cle"] = false; // initialiser clé a 0
    }
} else {
    $req = $db->query("SELECT id FROM couloir WHERE type = 'depart' LIMIT 1");
    $row = $req->fetchArray(SQLITE3_ASSOC);
    $position = $row["id"];
}

$info = $db->query("SELECT type FROM couloir WHERE id = $position")->fetchArray(SQLITE3_ASSOC);
$type_actuel = $info["type"] ?? "inconnu";

if ($type_actuel === "cle") {
    $_SESSION["cle"] = true;
}

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

// DEPLACEMENT
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

echo "<h1>Position : Couloir $position (type : $type_actuel)</h1>";

echo $_SESSION["cle"]
    ? "<p><b>Inventaire : Une clé est disponible </b></p>"
    : "<p><b>Inventaire : aucune clé</b></p>";

echo "<h2>Déplacements possibles :</h2><ul>";

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

    $couloir_dispo = $row["couloir_dispo"];
    $direction = normaliserDirection($row["direction"]);
    $direction_text = directionFull($direction);
    $type_passage = $row["type_passage"];

    //  Passage bloqué si grille et pas de clé
    if ($type_passage === "grille" && $_SESSION["cle"] === false) {
        echo "<li>Le couloir $couloir_dispo est bloqué 🔒 (grille, pas de clé)</li>";
        continue;
    }

    //  Passage grille avec clé → consommation de la clé
    if ($type_passage === "grille" && $_SESSION["cle"] === true) {
        echo "<li>Le couloir $couloir_dispo est vérouillé, souhaites tu utilisé ta clé? <a href='?pos=$couloir_dispo&grid_opened=1'>oui</a> ? ($direction_text)
                
              </li>";
        continue;
    }

    //  Passage libre
    echo "<li>Le couloir $couloir_dispo est disponible, y <a href='?pos=$couloir_dispo'>aller</a> ? ($direction_text)
            
          </li>";
}

echo "</ul>";
?>

