<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ================= CONNEXION BD =================
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=saim;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur DB : " . $e->getMessage());
}

// ================= AJAX =================
if (isset($_POST['ajax'])) {

    $res = [];

    // 🔎 RECHERCHE
    if (isset($_POST['recherche']) && trim($_POST['recherche']) !== "") {

        $r = "%" . $_POST['recherche'] . "%";

        $sql = "
            SELECT titre, fichier_pdf FROM cours
            WHERE titre LIKE ?
            UNION ALL
            SELECT titre, fichier_pdf FROM exercices
            WHERE titre LIKE ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$r, $r]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    // 🎯 FILTRE (cours / exo)
    elseif (
        isset($_POST['type'], $_POST['niveau'], $_POST['annes'])
    ) {

        $type   = $_POST['type'];
        $niveau = $_POST['niveau'];
        $annee  = $_POST['annes'];

        if (!in_array($type, ["cours", "exercice"])) {
            exit("Type invalide");
        }

        $table = ($type === "cours") ? "cours" : "exercices";

        $stmt = $pdo->prepare(
            "SELECT titre, fichier_pdf FROM $table
             WHERE niveau = ? AND annee = ?"
        );
        $stmt->execute([$niveau, $annee]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 📤 RETOUR AJAX
    if (empty($res)) {
        echo "<p style='opacity:.6'>Aucun résultat</p>";
        exit;
    }

    foreach ($res as $r) {
        $titre = htmlspecialchars($r['titre']);
        $file  = urlencode($r['fichier_pdf']);

        echo "
        <div class='card'>
            <h3>$titre</h3>
            <a href='?download=$file&titre=" . urlencode($titre) . "'>
                📄 Télécharger PDF
            </a>
        </div>";
    }
    exit;
}

// ================= HISTORIQUE DOWNLOAD =================
if (isset($_GET['download'])) {

    $fichier = basename($_GET['download']); // sécurité
    $titre   = $_GET['titre'] ?? "";
    $ip      = $_SERVER['REMOTE_ADDR'];

    $path = __DIR__ . "/uploads/" . $fichier;

    if (!file_exists($path)) {
        die("Fichier introuvable");
    }

    $stmt = $pdo->prepare(
        "INSERT INTO historique_download (titre, fichier, ip_user)
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$titre, $fichier, $ip]);

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"$fichier\"");
    readfile($path);
    exit;
}
?>
