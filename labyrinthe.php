<?php
session_start();

// Connexion à la base SQLite
$db = new SQLite3("labyrinthe.db");

// ---------------------------
// Initialisation de la session
// ---------------------------
if (!isset($_SESSION["nbCle"])) $_SESSION["nbCle"] = 0;
if (!isset($_SESSION["cles_ramassees"])) $_SESSION["cles_ramassees"] = [];
if (!isset($_SESSION["pas"])) $_SESSION["pas"] = 0;

// ---------------------------
// Gestion du reset
// ---------------------------
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ---------------------------
// Récupérer la position actuelle
// ---------------------------
if (isset($_GET["position"])) {
    $position = (int)$_GET["position"];

    if (!isset($_SESSION["position_precedente"]) || $_SESSION["position_precedente"] != $position) {
        $_SESSION["pas"] += 1;
        $_SESSION["position_precedente"] = $position;
    }

    if (isset($_GET["grille_ouverte"]) && $_SESSION["nbCle"] > 0) {
        $_SESSION["nbCle"] -= 1;
    }

} else {
    $req = $db->query("SELECT id FROM couloir WHERE type = 'depart' LIMIT 1");
    $row = $req->fetchArray(SQLITE3_ASSOC);
    $position = $row["id"];
}

// ---------------------------
// Récupérer le type de la case actuelle
// ---------------------------
$stmt = $db->prepare("SELECT type FROM couloir WHERE id = :id");
$stmt->bindValue(":id", $position, SQLITE3_INTEGER);
$info = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
$type_actuel = $info["type"] ?? "inconnu";

// Condition de fin
if ($type_actuel === "sortie") {
    $_SESSION["etape"] = "fin";
    header("Location: index.php");
    exit;
}

// Si on marche sur une clé
if ($type_actuel === "cle" && !in_array($position, $_SESSION["cles_ramassees"])) {
    $_SESSION["nbCle"] += 1;
    $_SESSION["cles_ramassees"][] = $position;
}

// ---------------------------
// Fonctions utilitaires
// ---------------------------
function normaliserDirection($dir) {
    $dir = strtoupper(trim($dir));
    return in_array($dir, ["N","S","E","O"]) ? $dir : "Secret";
}
function directionFull($dir) {
    $map = ["N" => "NORD", "S" => "SUD", "E" => "EST", "O" => "OUEST"];
    return $map[$dir] ?? "SECRET";
}

// ---------------------------
// Récupérer les couloirs accessibles
// ---------------------------
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Labyrinthe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1, h2 {
            text-align: center;
            color: #2c3e50;
        }
        .box {
            background-color: #fff;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .inventory, .steps {
            font-weight: bold;
            margin-bottom: 10px;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        button {
            padding: 10px 25px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
        }
        button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <h1>Position : Couloir <?php echo $position; ?> (<?php echo $type_actuel; ?>)</h1>

    <div class="box inventory">
        Inventaire : <?php echo $_SESSION["nbCle"] > 0 ? $_SESSION["nbCle"]." clé(s)" : "aucune clé"; ?>
    </div>

<<<<<<< HEAD
    <div class="box steps">
        Nombre de pas effectués : <?php echo $_SESSION["pas"]; ?>
    </div>
=======
// Inventaire
echo $_SESSION["cle"] // Ajt compteur cle + cle utilisable une fois
    ? "<p><b>Inventaire : Clé disponible 🔑</b></p>"
    : "<p><b>Inventaire : aucune clé</b></p>";
>>>>>>> 162686a88ecd3b2d9485d4c3bc8eb6c16b32b645

    <div class="box">
        <h2>Déplacements possibles :</h2>
        <ul>
        <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)):
            $couloir_dispo = $row["couloir_dispo"];
            $direction = normaliserDirection($row["direction"]);
            $direction_text = directionFull($direction);
            $type_passage = $row["type_passage"];
        ?>
            <?php if ($type_passage === "grille"): ?>
                <?php if ($_SESSION["nbCle"] > 0): ?>
                    <li>Le couloir <?php echo $couloir_dispo; ?> est verrouillé (<?php echo $direction_text; ?>), 
                        <a href="?position=<?php echo $couloir_dispo; ?>&grille_ouverte=1">Ouvrir avec une clé</a>
                    </li>
                <?php else: ?>
                    <li>Le couloir <?php echo $couloir_dispo; ?> est bloqué (grille, pas de clé)</li>
                <?php endif; ?>
            <?php else: ?>
                <li>Le couloir <?php echo $couloir_dispo; ?> est disponible (<?php echo $direction_text; ?>), 
                    <a href="?position=<?php echo $couloir_dispo; ?>">Aller</a>
                </li>
            <?php endif; ?>
        <?php endwhile; ?>
        </ul>
    </div>

    <form method="get" action="" class="box">
        <button type="submit" name="reset" value="1">Recommencer une nouvelle partie</button>
    </form>

<<<<<<< HEAD
</body>
</html>
=======
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
>>>>>>> 162686a88ecd3b2d9485d4c3bc8eb6c16b32b645
