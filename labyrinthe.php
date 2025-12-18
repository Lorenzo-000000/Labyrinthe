<?php
if (session_status() === PHP_SESSION_NONE) 
    { 
        session_start(); 
    }

// ===================
// Chrono côté serveur
// ===================
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

$temps_max = 3 * 60; 
$temps_ecoule = time() - $_SESSION['start_time'];

if ($temps_ecoule > $temps_max) {
    session_destroy();
    header("Location: game over.php");
    exit;
}

// ===================
// Base de données
// ===================
$db = new SQLite3("labyrinthe.db");

// Initialisation session
$_SESSION["nbCle"] ??= 0;
$_SESSION["cles_ramassees"] ??= [];
$_SESSION["pas"] ??= 0;

// Reset partie
if (isset($_GET["reset"])) {
    session_destroy();
    header("Location: ".$_SERVER["PHP_SELF"]);
    exit;
}

// Position du joueur
$position = isset($_GET["position"]) ? (int)$_GET["position"] : $db->query("SELECT id FROM couloir WHERE type='depart' LIMIT 1")->fetchArray(SQLITE3_ASSOC)["id"];

if (!isset($_SESSION["position_precedente"]) || $_SESSION["position_precedente"] !== $position) {
    $_SESSION["pas"]++;
    $_SESSION["position_precedente"] = $position;
}

if (isset($_GET["grille_ouverte"]) && $_SESSION["nbCle"] > 0) {
    $_SESSION["nbCle"]--;
}

// Type du couloir
$type_actuel = $db->prepare("SELECT type FROM couloir WHERE id = :id");
$type_actuel->bindValue(":id", $position, SQLITE3_INTEGER);
$type_actuel = $type_actuel->execute()->fetchArray(SQLITE3_ASSOC)["type"] ?? "inconnu";

// Sortie
if ($type_actuel === "sortie") {
    header("Location: fin.php");
    exit;
}

// Ramasser une clé
if ($type_actuel === "cle" && !in_array($position, $_SESSION["cles_ramassees"])) {
    $_SESSION["nbCle"]++;
    $_SESSION["cles_ramassees"][] = $position;
}

// ===================
// Fonctions
// ===================
function normaliserDirection(string $dir): ?string {
    $dir = strtoupper(trim($dir));
    return in_array($dir, ["N","S","E","O","SECRET"]) ? $dir : null;
}

function flecheVide(string $dir): string {
    return "<span class='fleche vide'><img src='images/$dir.png' class='fleche-img'></span>";
}

// ===================
// Passages
// ===================
$sql = "
SELECT
    CASE WHEN couloir1 = :pos THEN couloir2 ELSE couloir1 END AS destination,
    CASE WHEN couloir1 = :pos THEN position2 ELSE position1 END AS direction,
    type AS type_passage
FROM passage
WHERE couloir1 = :pos OR couloir2 = :pos
";
$stmt = $db->prepare($sql);
$stmt->bindValue(":pos", $position, SQLITE3_INTEGER);
$result = $stmt->execute();

$dirs = ["N"=>null,"S"=>null,"E"=>null,"O"=>null,"SECRET"=>null];
$img = ["N"=>"images/nord.png","S"=>"images/sud.png","E"=>"images/est.png","O"=>"images/ouest.png","SECRET"=>"images/secret.png"];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $dest = $row["destination"];
    $type = strtolower($row["type_passage"]);
    $dir = normaliserDirection($row["direction"]);

    if ($type === "secret") {
        $dirs["SECRET"] = "<a class='fleche secret' href='?position=$dest'><img src='images/secret.png' class='fleche-img'></a>";
        continue;
    }

    if ($dir === null) continue;
    $image = "<img src='{$img[$dir]}' alt='$dir' class='fleche-img'>";

    if ($type === "grille" && $_SESSION["nbCle"] === 0) {
        $dirs[$dir] = "<span class='fleche bloquee'>$image</span>";
    } else {
        $link = "?position=$dest";
        if ($type === "grille") $link .= "&grille_ouverte=1";
        $dirs[$dir] = "<a class='fleche' href='$link'>$image</a>";
    }
}

// Temps restant
$minutes = floor(($temps_max - $temps_ecoule) / 60);
$secondes = ($temps_max - $temps_ecoule) % 60;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Labyrinthe</title>
<link rel="stylesheet" href="css/style.css">
<style>
body {
    background: url("images/mid.png") no-repeat center center fixed;
    background-size: cover;
    color: white;
    font-family: Arial, sans-serif;
    padding: 20px;
}
.dpad {
    display: grid;
    grid-template-columns: repeat(3, 80px);
    grid-template-rows: repeat(3, 80px);
    gap: 6px;
    margin-top: 20px;
}
.fleche-img { width: 64px; }
.bloquee { opacity: 0.3; pointer-events: none; }
.vide { opacity: 0.15; }
.secret {
    filter: drop-shadow(0 0 6px gold);
    animation: pulse 1.4s infinite;
}
@keyframes pulse {
    0% { opacity: .5; }
    50% { opacity: 1; }
    100% { opacity: .5; }
}
</style>
</head>
<body>

<h1>Couloir n°<?= $position ?> (<?= $type_actuel ?>)</h1>

<p>Inventaire : <?= $_SESSION["nbCle"] > 0 ? $_SESSION["nbCle"]." clé(s) 🔑" : "aucune clé" ?></p>
<p>Nombre de pas : <?= $_SESSION["pas"] ?></p>
<p>Temps restant : <?= $minutes ?> min <?= str_pad($secondes,2,'0',STR_PAD_LEFT) ?> sec</p>

<h2>Déplacements possibles</h2>
<div class="dpad">
    <div></div>
    <?= $dirs["N"] ?? flecheVide("nord") ?>
    <div></div>

    <?= $dirs["O"] ?? flecheVide("ouest") ?>
    <?= $dirs["SECRET"] ?? "<div></div>" ?>
    <?= $dirs["E"] ?? flecheVide("est") ?>

    <div></div>
    <?= $dirs["S"] ?? flecheVide("sud") ?>
    <div></div>
</div>

<p><a href="?reset=1">Recommencer la partie</a></p>

</body>
</html>
